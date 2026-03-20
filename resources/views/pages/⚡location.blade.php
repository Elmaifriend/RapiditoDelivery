<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use App\Models\DeliveryAddress;

new #[Title('Buscar Dirección')] class extends Component {

    public $lat;
    public $lng;
    public $googleResult = null;
    public $formattedAddress;

    #[Url]
    public string $mode = 'settings';

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
            'language' => 'es'
        ]);

        $data = $response->json();

        if (isset($data['results'][0])) {
            $this->googleResult = $data['results'][0];
            $this->formattedAddress = $this->googleResult['formatted_address'];
        } else {
            $this->googleResult = null;
            $this->formattedAddress = "Ubicación desconocida";
        }
    }

    public function save()
    {
        if (!$this->lat || !$this->lng || !$this->googleResult) return;

        $guestToken = request()->cookie('guest_token');
        $components = $this->googleResult['address_components'];

        $calle = $this->extractComponent($components, ['route']);
        $numero = $this->extractComponent($components, ['street_number']);
        $colonia = $this->extractComponent($components, ['sublocality', 'sublocality_level_1', 'neighborhood']);
        $ciudad = $this->extractComponent($components, ['locality', 'administrative_area_level_2']);
        $estado = $this->extractComponent($components, ['administrative_area_level_1']);
        $codigoPostal = $this->extractComponent($components, ['postal_code']);
        $pais = $this->extractComponent($components, ['country']);

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
                'place_id' => $this->googleResult['place_id'] ?? null,
            ]
        );

        if ($this->mode === 'checkout') {
            $this->redirect('/cart', navigate: true);
        } else {
            $this->dispatch('addressUpdated');
            $this->redirect('/', navigate: true);
        }
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
};
?>

{{-- El componente Alpine completo en línea. Se ejecuta CADA VEZ que Livewire lo inyecta al DOM --}}
<div x-data="{
        map: null,
        autocomplete: null,
        loadingLocation: false,

        // init() arranca mágicamente en cuanto pisas esta página
        init() {
            this.initLeaflet();
            this.initGooglePlaces();
        },

        initLeaflet() {
            const container = document.getElementById('map');
            if(!container) return;

            // Limpiar instancia previa para Livewire Navigate
            if (container._leaflet_id) {
                container._leaflet_id = null;
            }

            // Mapa con controles ocultos para minimalismo
            this.map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([32.5149, -117.0382], 16);

            // MAPA SIMPLIFICADO: CartoDB Positron (Gris claro, pocos detalles)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20
            }).addTo(this.map);

            setTimeout(() => this.map.invalidateSize(), 200);

            this.map.on('moveend', () => {
                let center = this.map.getCenter();

                // Usamos $wire que Livewire inyecta automáticamente en Alpine
                this.$wire.updateLocation(center.lat, center.lng);

                if(this.autocomplete && typeof google !== 'undefined') {
                    let bounds = this.map.getBounds();
                    this.autocomplete.setBounds(new google.maps.LatLngBounds(
                        new google.maps.LatLng(bounds.getSouthWest().lat, bounds.getSouthWest().lng),
                        new google.maps.LatLng(bounds.getNorthEast().lat, bounds.getNorthEast().lng)
                    ));
                }
            });
        },

        locateMe() {
            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización');
                return;
            }

            this.loadingLocation = true;
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    this.map.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });
                    this.loadingLocation = false;
                },
                (error) => {
                    this.loadingLocation = false;
                    alert('Permiso de ubicación denegado o error de señal.');
                },
                { enableHighAccuracy: true }
            );
        },

        initGooglePlaces() {
            const input = document.getElementById('google-places-input');
            if(!input) return;

            // Vigilamos hasta que el script de Google (que está en tu layout) termine de descargar
            const checkGoogle = () => {
                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    input.addEventListener('keydown', (e) => { if(e.key === 'Enter') e.preventDefault() });

                    this.autocomplete = new google.maps.places.Autocomplete(input, {
                        componentRestrictions: { country: 'mx' },
                        fields: ['geometry', 'name'],
                    });

                    this.autocomplete.addListener('place_changed', () => {
                        const place = this.autocomplete.getPlace();
                        if (place.geometry && place.geometry.location) {
                            this.map.flyTo([place.geometry.location.lat(), place.geometry.location.lng()], 16, { animate: true, duration: 1.5 });
                        }
                    });
                } else {
                    setTimeout(checkGoogle, 100);
                }
            };

            checkGoogle();
        }
    }"
    class="fixed inset-0 z-[100] h-[100dvh] flex flex-col bg-white overflow-hidden"
>
    {{-- BARRA DE BÚSQUEDA SUPERIOR --}}
    <div class="absolute top-6 left-0 right-0 z-50 p-4 pointer-events-none">
        <div class="max-w-md mx-auto flex flex-col gap-3">
            <div class="flex items-center gap-2 pointer-events-auto">
                <a wire:navigate href="/" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-800 active:scale-95 transition-all">
                    <i class="bxf bx-chevron-left text-3xl"></i>
                </a>

                <div class="relative flex-1" wire:ignore>
                    <input
                        id="google-places-input"
                        type="text"
                        placeholder="¿A dónde enviamos?"
                        class="w-full h-12 border-none rounded-2xl p-3 pl-11 bg-white shadow-xl focus:ring-2 focus:ring-red-600 text-sm text-gray-700"
                    />
                    <i class="bxf bx-search absolute left-4 top-3.5 text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 relative z-0" wire:ignore>
        <div id="map" class="absolute inset-0"></div>

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[90%] z-[1000] pointer-events-none flex flex-col items-center">
            <div class="relative group">
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-1.5 bg-black/20 rounded-[100%] blur-[1px]"></div>

                <div class="relative animate-bounce-slow">
                    <svg width="50" height="60" viewBox="0 0 50 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-2xl">
                        <path d="M25 0C11.1929 0 0 11.1929 0 25C0 39.5 25 60 25 60C25 60 50 39.5 50 25C50 11.1929 38.8071 0 25 0Z" fill="#e7000b"/>
                        <circle cx="25" cy="24" r="18" fill="white"/>
                    </svg>
                    <div class="absolute top-[12px] left-[13px]">
                        <i class="bxf bx-carrot text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <button
            x-on:click="locateMe()"
            class="absolute bottom-8 right-4 z-[1000] flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-2xl text-red-600 active:scale-90 transition-all border border-gray-100"
        >
            <template x-if="!loadingLocation">
                <i class="bxf bx-location-pin text-2xl"></i>
            </template>
            <template x-if="loadingLocation">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-orange-100 border-t-orange-600"></div>
            </template>
        </button>
    </div>

    {{-- PANEL INFERIOR --}}
    <div class="p-4 bg-white rounded-t-3xl shadow-[0_-10px_30px_rgba(0,0,0,0.08)] z-10 flex-shrink-0 flex flex-col gap-4">

        <div class="flex items-start gap-3 p-2">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="bxf bx-location-plus text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Dirección seleccionada</p>
                <p class="text-sm font-semibold text-gray-800 line-clamp-2 mt-0.5 leading-snug">
                    @if($formattedAddress)
                        {{ $formattedAddress }}
                    @else
                        Mueve el mapa para seleccionar...
                    @endif
                </p>
            </div>
        </div>

        <button
            wire:click="save"
            @if(!$formattedAddress) disabled @endif
            class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100 shadow-md shadow-red-200"
        >
            {{ $mode === 'checkout' ? 'Confirmar Ubicación' : 'Guardar ubicación' }}
        </button>

    </div>
</div>
