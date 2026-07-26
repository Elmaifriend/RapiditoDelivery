<div x-data="{
        map: null,
        autocomplete: null,
        loadingLocation: false,

        init() {
            this.initLeaflet();
            this.initGooglePlaces();
            if (!this.$wire.lat) {
                this.locateMe();
            }
        },

        initLeaflet() {
            const container = document.getElementById('map');
            if(!container) return;

            if (container._leaflet_id) {
                container._leaflet_id = null;
            }

            const startLat = this.$wire.lat ? this.$wire.lat : 23.6345;
            const startLng = this.$wire.lng ? this.$wire.lng : -102.5528;
            const zoomLevel = this.$wire.lat ? 16 : 5;

            this.map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([startLat, startLng], zoomLevel);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20
            }).addTo(this.map);

            setTimeout(() => this.map.invalidateSize(), 200);

            this.map.on('moveend', () => {
                let center = this.map.getCenter();

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
                            const newLat = place.geometry.location.lat();
                            const newLng = place.geometry.location.lng();
                            
                            this.map.flyTo([newLat, newLng], 16, { animate: true, duration: 1.5 });
                            this.$wire.updateLocation(newLat, newLng);
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
                <button onclick="history.back();" class="cursor-pointer flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-800 active:scale-95 transition-all">
                    <i class="bxf bx-chevron-left text-3xl"></i>
                </button>

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

    {{-- ZONA DEL MAPA --}}
    <div class="flex-1 relative z-0" wire:ignore>
        <div id="map" class="absolute inset-0"></div>

        {{-- Pin Central Fijo --}}
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

        {{-- Botón de Mi Ubicación --}}
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
