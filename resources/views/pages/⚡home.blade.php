<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\ServiceZone;

new #[Title('Home')] class extends Component {

    public ?float $lat = null;
    public ?float $lng = null;

    public $city = null;
    public bool $noService = false;
    public bool $locationDenied = false;

    protected $listeners = [
        'locationDetected' => 'setLocation',
        'locationDenied' => 'handleDenied',
    ];

    public function handleDenied()
    {
        $this->locationDenied = true;
        $this->noService = false;
        $this->city = null;
    }

    public function setLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;

        $this->resolveServiceZone();
    }

    protected function resolveServiceZone()
    {
        $zone = ServiceZone::active()
            ->with('city')
            ->get()
            ->first(fn ($zone) => $zone->contains($this->lat, $this->lng));

        if (!$zone) {
            $this->noService = true;
            return;
        }

        $this->city = $zone->city;
        $this->noService = false;
    }
};
?>

<div class="flex flex-col gap-4 pt-4">
    {{-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant --}}

    @if($locationDenied)
        <div class="bg-yellow-100 text-yellow-700 p-4 rounded-xl">
            Necesitamos acceso a tu ubicación para mostrar restaurantes disponibles.
        </div>
    @endif

    @if($city)
        <div class="mx-4 mt-4 rounded-xl bg-green-100 p-4 text-sm text-green-700">
            Estás en {{ $city->name }}
        </div>
    @endif

    @if($noService)
        <div class="mx-4 mt-4 rounded-xl bg-red-100 p-4 text-sm text-red-600">
            No tenemos servicio en tu ubicación, estamos trabajando para llegar a tu ciudad.
        </div>
    @endif

    <a href="{{ route('search') }}" class="mx-4 flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4">
        <i class="bxf bx-search text-lg text-red-400"></i>
        <p class="text-sm font-medium text-gray-400">¿Qué se te antoja hoy? </p>
    </a>

    <div class="no-scrollbar flex w-full gap-4 overflow-y-auto px-4">
        <div
            class="rounded-4xl bg-linear-to-r relative flex h-40 min-w-[85%] flex-col justify-center overflow-hidden from-red-400 to-red-600 p-5 text-white shadow-lg shadow-red-200">
            <div class="z-10 flex flex-col gap-4">
                <div>
                    <h2 class="font-logo text-2xl font-extrabold tracking-tight">50% OFF</h2>
                    <p class="text-sm font-medium opacity-90">En tu primer pedido</p>
                </div>
                <a class="self-start rounded-xl bg-white px-4 py-2 text-xs font-bold text-red-600">Ver más</a>
            </div>
            <i class="bxf bx-carrot absolute -bottom-6 -right-2 rotate-12 text-9xl opacity-20"></i>
        </div>

        <div
            class="rounded-4xl bg-linear-to-r relative flex h-40 min-w-[85%] flex-col justify-center overflow-hidden from-orange-300 to-red-400 p-5 text-white shadow-lg shadow-red-200">
            <div class="z-10">
                <h2 class="font-logo text-2xl font-extrabold tracking-tight">Envíos Gratis</h2>
                <p class="text-sm font-medium opacity-90">Todo el fin de semana</p>
            </div>
            <i class="bxf bx-bolt rotate-15 absolute -bottom-6 -right-6 text-9xl opacity-20"></i>
        </div>
    </div>

    <div class="flex w-full flex-col gap-2">
        <h2 class="px-4 font-bold">Categorías</h2>

        <div class="no-scrollbar flex gap-12 overflow-y-auto px-4">
            <livewire:category.icon category="Pizza" icon="🍕" />
            <livewire:category.icon category="Hamburguesas" icon="🍔" />
            <livewire:category.icon category="Tacos" icon="🌮" />
            <livewire:category.icon category="Sushi" icon="🍣" />
            <livewire:category.icon category="Pollo" icon="🍗" />
            <livewire:category.icon category="Comida Mexicana" icon="🇲🇽" />
            <livewire:category.icon category="Saludable" icon="🥗" />
            <livewire:category.icon category="Desayunos" icon="🍳" />
            <livewire:category.icon category="Postres" icon="🍰" />
            <livewire:category.icon category="Café" icon="☕" />
            <livewire:category.icon category="Helados" icon="🍦" />
            <livewire:category.icon category="Comida Asiática" icon="🥢" />
            <livewire:category.icon category="Mariscos" icon="🦐" />
            <livewire:category.icon category="Bebidas" icon="🥤" />
        </div>
    </div>

    <div class="flex w-full flex-col gap-2 px-4">
        <h2 class="font-bold">Restaurantes cerca</h2>

        <div class="flex flex-col gap-4">
            <livewire:restaurant.card name="El Tizoncito" type="Tacos" :stars="3.5" time="35-40min"
                image="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=500&q=80" />

            <livewire:restaurant.card name="Burger Lab" type="Hamburguesas" :stars="4.2" time="25-30min"
                image="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=500&q=80" />

            <livewire:restaurant.card name="Sushi Itto" type="Sushi" :stars="4.0" time="40-50min"
                image="https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=500&q=80" />

            <livewire:restaurant.card name="La Casa del Pollo" type="Pollo" :stars="3.8" time="30-35min"
                image="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=500&q=80" />

            <livewire:restaurant.card name="Green Bowl" type="Saludable" :stars="4.5" time="20-25min"
                image="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=500&q=80" />

            <livewire:restaurant.card name="Dulce Antojo" type="Postres" :stars="4.7" time="15-20min"
                image="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=500&q=80" />
        </div>
    </div>

    <script>
    document.addEventListener('livewire:navigated', () => {

        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                Livewire.dispatch('locationDetected', [
                    position.coords.latitude,
                    position.coords.longitude
                ]);
            },
            function (error) {
                console.log('Geolocation error:', error);
                Livewire.dispatch('locationDenied');
            }
        );
    });
    </script>

</div>
