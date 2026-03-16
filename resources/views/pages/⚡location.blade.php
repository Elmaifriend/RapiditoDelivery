<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DeliveryAddress;

new #[Title('Buscar Dirección')] class extends Component {

    public $search = '';
    public $lat;
    public $lng;
    
    // Ahora guardaremos la respuesta completa de Google aquí temporalmente
    public $googleResult = null; 
    public $formattedAddress;

    #[Url] 
    public string $mode = 'settings'; // settings | checkout

    // Escuchamos el evento del mapa
    #[On('mapMoved')]
    public function updateLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;

        $this->reverseGeocode();
    }

    public function reverseGeocode()
    {
        if (!$this->lat || !$this->lng) return;

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$this->lat},{$this->lng}",
            'key' => config('services.google_maps.key'),
            'language' => 'es' // Siempre en español
        ]);

        $data = $response->json();

        if (isset($data['results'][0])) {
            // Guardamos todo el resultado para usarlo luego en la función save()
            $this->googleResult = $data['results'][0]; 
            $this->formattedAddress = $this->googleResult['formatted_address'];
        } else {
            $this->googleResult = null;
            $this->formattedAddress = "Ubicación desconocida";
        }
    }

    public function save()
    {
        // No guardamos si no tenemos coordenadas o si Google no nos dio datos
        if (!$this->lat || !$this->lng || !$this->googleResult) return;

        $guestToken = request()->cookie('guest_token');
        $components = $this->googleResult['address_components'];

        // Usamos la misma lógica precisa del Home
        $calle = $this->extractComponent($components, ['route']);
        $numero = $this->extractComponent($components, ['street_number']);
        $colonia = $this->extractComponent($components, ['sublocality', 'sublocality_level_1', 'neighborhood']);
        $ciudad = $this->extractComponent($components, ['locality', 'administrative_area_level_2']);
        $estado = $this->extractComponent($components, ['administrative_area_level_1']);
        $codigoPostal = $this->extractComponent($components, ['postal_code']);
        $pais = $this->extractComponent($components, ['country']);
        $placeId = $this->googleResult['place_id'] ?? null;

        DeliveryAddress::updateOrCreate(
            ['guest_token' => $guestToken],
            [
                'formatted_address' => $this->googleResult['formatted_address'],
                'street' => trim($calle . ' ' . $numero),
                'street_number' => $numero,
                'neighborhood' => $colonia,
                'city' => $ciudad,
                'state' => $estado,
                'postal_code' => $codigoPostal,
                'country' => $pais ?? 'México',
                'lat' => $this->lat,
                'lng' => $this->lng,
                'place_id' => $placeId,
            ]
        );

        // Flujo diferente dependiendo del modo
        if ($this->mode === 'checkout') {
            $this->redirect('/cart', navigate: true);
        } else {
            $this->dispatch('addressUpdated');
            $this->redirect('/', navigate: true);
        }
    }

    // Función ayudante (La misma del Home)
    protected function extractComponent(array $components, array $types): ?string
    {
        foreach ($components as $component) {
            if (!empty(array_intersect($types, $component['types']))) {
                return $component['long_name'];
            }
        }
        return null;
    }
};
?>

<div class="h-screen flex flex-col bg-gray-50">

    {{-- HEADER --}}
    <div class="p-4 bg-white shadow z-10 flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <a wire:navigate href="/" class="p-2 text-gray-500 rounded-full hover:bg-gray-100">
                <i class="bxf bx-arrow-back text-xl"></i>
            </a>
            <h1 class="font-bold text-lg">Elige tu ubicación</h1>
        </div>

        <input 
            type="text"
            wire:model.live.debounce.500ms="search"
            placeholder="Buscar dirección..."
            class="w-full border-2 border-gray-100 rounded-xl p-3 bg-gray-50 focus:border-red-400 focus:outline-none focus:bg-white transition-colors"
        />
    </div>

    {{-- MAPA --}}
    <div id="map" wire:ignore class="flex-1 z-0 relative">
        {{-- Indicador de carga súper simple para cuando el usuario mueve el mapa --}}
        <div wire:loading wire:target="updateLocation" class="absolute top-4 left-1/2 -translate-x-1/2 bg-black/70 text-white text-xs px-3 py-1 rounded-full z-[1000]">
            Buscando calle...
        </div>
    </div>

    {{-- PANEL INFERIOR --}}
    <div class="p-4 bg-white rounded-t-3xl shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-10 flex flex-col gap-4">

        <div class="flex items-start gap-3 p-2">
            <i class="bxf bx-map text-red-500 text-2xl mt-1"></i>
            <div>
                <p class="text-xs text-gray-500 font-medium">Dirección seleccionada</p>
                <p class="text-sm font-semibold text-gray-800 line-clamp-2">
                    @if($formattedAddress)
                        {{ $formattedAddress }}
                    @else
                        Mueve el pin en el mapa...
                    @endif
                </p>
            </div>
        </div>

        {{-- BOTÓN DINÁMICO (Ahora ejecutan wire:click="save") --}}
        <button
            wire:click="save"
            {{-- Deshabilitamos el botón si no hay dirección todavía --}}
            @if(!$formattedAddress) disabled @endif
            class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100"
        >
            {{ $mode === 'checkout' ? 'Siguiente' : 'Guardar dirección' }}
        </button>

    </div>

    {{-- SCRIPT MAPA --}}
    <script>
        document.addEventListener('livewire:navigated', () => {

            if (window.mapInstance) {
                window.mapInstance.remove();
            }

            const map = L.map('map').setView([32.5149, -117.0382], 15);
            window.mapInstance = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const marker = L.marker(map.getCenter(), {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function() {
                let pos = marker.getLatLng();
                Livewire.dispatch('mapMoved', {
                    lat: pos.lat,
                    lng: pos.lng
                });
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                Livewire.dispatch('mapMoved', {
                    lat: e.latlng.lat,
                    lng: e.latlng.lng
                });
            });
        });
    </script>
</div>