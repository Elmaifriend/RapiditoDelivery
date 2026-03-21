<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\ServiceZone;
use App\Models\DeliveryAddress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use App\Models\Category;

new #[Title('Home')] class extends Component {
    public ?int $selectedCategoryId = null;

    public ?float $lat = null;
    public ?float $lng = null;

    public $city = null;
    public bool $noService = false;
    public bool $locationDenied = false;

    protected $listeners = [
        'locationDetected' => 'setLocation',
        'locationDenied' => 'handleDenied',
        'category-selected' => 'filterByCategory',
    ];

    public function handleDenied()
    {
        $this->locationDenied = true;
        $this->noService = false;
        $this->city = null;

        // Disparamos el único evento global para que el header reaccione
        $this->dispatch('addressUpdated');
    }

    // Agregamos este método al principio de tu clase Home
    public function mount()
    {
        $guestToken = request()->cookie('guest_token');
        $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

        // Si ya tenemos una dirección guardada en la base de datos...
        if ($address && $address->lat && $address->lng) {
            $this->lat = $address->lat;
            $this->lng = $address->lng;

            // Calculamos los restaurantes directamente sin consultar a Google
            $this->resolveServiceZone();
        }
    }

    // Tu método setLocation se queda casi igual, solo delegamos la resolución
    public function setLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;

        // Consultamos a Google y guardamos en BD
        $this->saveAddress();

        // Evaluamos los restaurantes
        $this->resolveServiceZone();

        // Avisamos al Header
        $this->dispatch('addressUpdated');
    }

    // Extraemos la lógica de la zona de servicio para poder reusarla
    protected function resolveServiceZone()
    {
        $zone = ServiceZone::active()
            ->with('city.businesses') // Precargamos los negocios para optimizar la consulta
            ->get()
            ->first(fn ($zone) => $zone->contains($this->lat, $this->lng));

        if (!$zone) {
            $this->noService = true;
            $this->city = null;
        } else {
            $this->noService = false;
            $this->city = $zone->city;
        }
    }

    protected function saveAddress()
    {
        $guestToken = request()->cookie('guest_token');

        if (!$this->lat || !$this->lng) return;

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$this->lat},{$this->lng}",
            'key' => config('services.google_maps.key'),
            'language' => 'es'
        ]);

        $data = $response->json();

        // Si Google no responde nada válido
        if (!isset($data['results'][0])) {
            \Illuminate\Support\Facades\Log::error('Error API Google:', $data ?? []);
            return;
        }

        $result = $data['results'][0];
        $components = $result['address_components'];

        // Extrayendo TODO el jugo a la API de Google
        $calle = $this->extractComponent($components, ['route']);
        $numero = $this->extractComponent($components, ['street_number']);
        $colonia = $this->extractComponent($components, ['sublocality', 'sublocality_level_1', 'neighborhood']);
        $ciudad = $this->extractComponent($components, ['locality', 'administrative_area_level_2']);
        $estado = $this->extractComponent($components, ['administrative_area_level_1']);
        $codigoPostal = $this->extractComponent($components, ['postal_code']);
        $pais = $this->extractComponent($components, ['country']);
        $placeId = $result['place_id'] ?? null; // Viene en la raíz del resultado, no en los componentes

        DeliveryAddress::updateOrCreate(
            // Condición de búsqueda (de quién es esta dirección)
            ['guest_token' => $guestToken],

            // Datos a actualizar o crear
            [
                'formatted_address' => $result['formatted_address'],
                'street' => $calle,
                'street_number' => $numero,
                'neighborhood' => $colonia,
                'city' => $ciudad,
                'state' => $estado,
                'postal_code' => $codigoPostal,
                'country' => $pais,
                'lat' => $this->lat,
                'lng' => $this->lng,
                'place_id' => $placeId,
            ]
        );

        // ¡Listo! Todo guardado. Ya no disparamos el evento aquí,
        // recuerda que se dispara al final del método setLocation()
    }

    protected function extractComponent(array $components, array $types): ?string
    {
        foreach ($components as $component) {
            if (!empty(array_intersect($types, $component['types']))) {
                return $component['long_name'];
            }
        }
        return null;
    }

    public function filterByCategory(?int $categoryId)
    {
        $this->selectedCategoryId = ($this->selectedCategoryId === $categoryId) ? null : $categoryId;
    }

    #[Computed]
    public function filteredBusinesses()
    {
        if (!$this->city) return collect();

        return $this->city->businesses()
            ->when($this->selectedCategoryId, function($query) {
                $query->where('category_id', $this->selectedCategoryId);
            })
            ->active()
            ->get();
    }
};
?>

{{-- EL HTML DE TU HOME SE QUEDA EXACTAMENTE IGUAL --}}

<div class="flex flex-col gap-4 pt-4">

    {{-- Estado de ubicación --}}
    @if($locationDenied)
    <div class="flex flex-col items-center justify-center px-6 py-24 gap-8 text-center">

        <span class="text-6xl text-red-500">
            <i class="bxf bx-lock-alt"></i>
        </span>

        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
            Necesitamos acceso a tu ubicación
        </h2>

        <p class="max-w-xs text-sm leading-relaxed text-gray-500">
            No pudimos acceder a tu ubicación. Necesitamos saber dónde estás para mostrarte los mejores sabores a tu
            alrededor.
        </p>

        <div class="flex flex-col gap-4">
            <p class="text-sm font-medium text-gray-400">
                ¿Prefieres hacerlo tú mismo?
            </p>

            <a wire:navigate href="/location"
                class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100 shadow-md shadow-red-200">
                Seleccionar ubicación manualmente
            </a>

            <p class="text-sm text-gray-400 italic">
                Prometemos no seguirte hasta tu cocina (solo hasta la puerta)
            </p>
        </div>
    </div>
    @elseif($city)

    {{-- Buscador
    <a href="{{ route('search') }}"
        class="mx-4 flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4">
        <i class="bxf bx-search text-lg text-red-400"></i>
        <p class="text-sm font-medium text-gray-400">
            ¿Qué se te antoja hoy?
        </p>
    </a> --}}

    {{-- Promos
    <div class="no-scrollbar flex w-full gap-4 overflow-x-auto px-4">

        <div
            class="relative flex h-40 min-w-[85%] flex-col justify-center overflow-hidden rounded-4xl bg-gradient-to-r from-red-400 to-red-600 p-5 text-white shadow-lg shadow-red-200">
            <div class="z-10 flex flex-col gap-4">
                <div>
                    <h2 class="font-logo text-2xl font-extrabold tracking-tight">
                        50% OFF
                    </h2>
                    <p class="text-sm font-medium opacity-90">
                        En tu primer pedido
                    </p>
                </div>
                <a class="self-start rounded-xl bg-white px-4 py-2 text-xs font-bold text-red-600">
                    Ver más
                </a>
            </div>
            <i class="bxf bx-carrot absolute -bottom-6 -right-2 rotate-12 text-9xl opacity-20"></i>
        </div>

        <div
            class="relative flex h-40 min-w-[85%] flex-col justify-center overflow-hidden rounded-4xl bg-gradient-to-r from-orange-300 to-red-400 p-5 text-white shadow-lg shadow-red-200">
            <div class="z-10">
                <h2 class="font-logo text-2xl font-extrabold tracking-tight">
                    Envíos Gratis
                </h2>
                <p class="text-sm font-medium opacity-90">
                    Todo el fin de semana
                </p>
            </div>
            <i class="bxf bx-bolt absolute -bottom-6 -right-6 text-9xl opacity-20"></i>
        </div>
    </div> --}}

    {{-- Categorías --}}
    <div class="flex w-full flex-col gap-2">
        <div class="flex items-center justify-between px-4">
            <h2 class="font-bold text-gray-800">Categorías</h2>
            @if($selectedCategoryId)
            <button wire:click="filterByCategory(null)" class="text-xs font-bold text-red-500">Limpiar</button>
            @endif
        </div>

        <div class="no-scrollbar flex gap-4 overflow-x-auto px-4 pb-2">
            @foreach(App\Models\Category::active()->get() as $cat)
            <livewire:category.icon
                :key="'cat-'.$cat->id"
                :id="$cat->id"
                :category="$cat->name"
                icon="🍴"
                :active="$selectedCategoryId === $cat->id"
            />
            @endforeach
        </div>
    </div>

    {{-- Restaurantes --}}
    <div class="flex w-full flex-col gap-2 px-4">
        <h2 class="font-bold text-gray-800">
            {{ $selectedCategoryId ? 'Resultados' : 'Restaurantes cerca' }}
        </h2>

    <div class="flex flex-col gap-4">
        @forelse($this->filteredBusinesses as $restaurant)
            <livewire:restaurant.card
                :key="'res-'.$restaurant->id"
                :business="$restaurant"
                :name="$restaurant->name"
                :type="$restaurant->category?->name ?? 'General'"
                :stars="4.0"
                time="30-40min"
                :image="$restaurant->banner_path
                        ? Storage::temporaryUrl($restaurant->banner_path, now()->addMinutes(10))
                        : 'https://picsum.photos/300/200'"
            />
        @empty
            <div class="flex flex-col items-center py-10 text-center">
                <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                <p class="mt-2 text-sm text-gray-400 text-balance">
                    No encontramos restaurantes de esta categoría en tu zona.
                </p>
            </div>
        @endforelse
    </div>
    </div>

    @elseif($noService)
    <div class="flex flex-col items-center justify-center px-6 py-24 gap-8 text-center">

        <span class="text-6xl text-red-500">:(</span>

        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
            ¡Vaya! Aún no llegamos ahí
        </h2>
        <p class="max-w-xs text-sm leading-relaxed text-gray-500">
            Lo sentimos mucho, pero aún no tenemos cobertura en tu ubicación actual.
        </p>

        <div class="flex flex-col gap-4">
            <p class="text-sm font-medium text-gray-400">
                ¿Crees que es un error?
            </p>

            <a wire:navigate href="/location"
                class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100 shadow-md shadow-red-200">
                Seleccionar ubicación manualmente
            </a>

            <p class="text-sm text-gray-400 italic">
                A veces el GPS tiene hambre y se confunde un poco
            </p>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:navigated', () => {
        // Obtenemos el token del usuario actual
        const guestToken = document.cookie
            .split('; ')
            .find(row => row.startsWith('guest_token='))
            ?.split('=')[1];

        // Verificamos si ya existe una dirección en la BD para este token.
        // Usamos una variable de Blade inyectada en el script.
        const hasAddress = @json(\App\Models\DeliveryAddress::where('guest_token', request()->cookie('guest_token'))->exists());

        // Si ya tiene dirección guardada (ya sea por GPS previo o manual), NO hacemos nada.
        // Dejamos que el Header simplemente la lea de la base de datos.
        if (hasAddress) {
            return;
        }

        // Si no tiene dirección (es su primera vez o borró cookies), pedimos el GPS
        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                Livewire.dispatch('locationDetected', {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                });
            },
            function (error) {
                console.log('Geolocation error:', error);
                Livewire.dispatch('locationDenied');
            }
        );
    });
    </script>
</div>
