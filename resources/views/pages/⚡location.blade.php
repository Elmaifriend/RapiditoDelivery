<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;
use App\Models\DeliveryAddress; // Asegúrate de importar tus modelos necesarios

new #[Title('Buscar Dirección')] class extends Component {
    
    public $search = '';
    public $lat;
    public $lng;
    public $formattedAddress;

    // Escuchamos el evento disparado desde JavaScript
    #[On('mapMoved')]
    public function updateLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;

        $this->reverseGeocode();
    }

    public function reverseGeocode()
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$this->lat},{$this->lng}",
            'key' => config('services.google_maps.key')
        ]);

        $data = $response->json();

        $this->formattedAddress = $data['results'][0]['formatted_address'] ?? null;
    }

    public function save()
    {
        // Validación básica
        if (!$this->lat || !$this->lng) return;

        // Aquí guardas la dirección en la BD (Adaptado a tu lógica del paso anterior)
        $guestToken = request()->cookie('guest_token');
        
        DeliveryAddress::updateOrCreate(
            ['guest_token' => $guestToken],
            [
                'formatted_address' => $this->formattedAddress,
                'lat' => $this->lat,
                'lng' => $this->lng,
                // 'city' => ... (puedes extraerla aquí si es necesario)
            ]
        );

        // Emitimos el evento global para que el Header lo escuche
        $this->dispatch('addressUpdated');

        // Redirigimos al usuario a la página principal
        $this->redirect('/', navigate: true);
    }
};
?>

<div class="h-screen flex flex-col bg-gray-50">

    {{-- Dependencias de Leaflet (Idealmente deberían ir en tu layout principal, pero aquí funcionan) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    {{-- Buscador superior --}}
    <div class="p-4 bg-white shadow z-10">
        <div class="flex items-center gap-2 mb-2">
            <a wire:navigate href="/" class="p-2 text-gray-500 rounded-full hover:bg-gray-100">
                <i class="bxf bx-arrow-back text-xl"></i>
            </a>
            <h1 class="font-bold text-lg">Elige tu ubicación</h1>
        </div>

        {{-- Cambiado a wire:model.live.debounce --}}
        <input 
            type="text"
            wire:model.live.debounce.500ms="search"
            placeholder="Buscar dirección..."
            class="w-full border-2 border-gray-100 rounded-xl p-3 bg-gray-50 focus:border-red-400 focus:outline-none focus:bg-white transition-colors"
        />
    </div>

    {{-- 
        Contenedor del Mapa 
        NOTA: wire:ignore es OBLIGATORIO aquí para que Livewire no destruya el mapa de Leaflet
    --}}
    <div id="map" wire:ignore class="flex-1 z-0"></div>

    {{-- Panel Inferior --}}
    <div class="p-4 bg-white rounded-t-3xl shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-10 flex flex-col gap-4">
        
        {{-- Muestra la dirección detectada para que el usuario sepa qué seleccionó --}}
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

        <button 
            wire:click="save"
            class="w-full bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50"
            @if(!$formattedAddress) disabled @endif
        >
            Confirmar dirección
        </button>

    </div>

    {{-- Script de inicialización del mapa --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Prevenimos inicializar el mapa dos veces si se navega de un lado a otro
            if (window.mapInstance) {
                window.mapInstance.remove();
            }

            // Coordenadas iniciales (Tijuana)
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

                // Enviar coordenadas a Livewire
                Livewire.dispatch('mapMoved', {
                    lat: pos.lat,
                    lng: pos.lng
                });
            });

            // Si el usuario hace click en el mapa, movemos el pin ahí
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