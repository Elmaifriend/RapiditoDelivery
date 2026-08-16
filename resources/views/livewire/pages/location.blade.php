<div
    class="animate-fade-in fixed top-0 left-0 z-[100] h-[var(--app-height,100svh)] w-full overflow-hidden bg-white"
    x-data="{
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
            if (!container) return;
    
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
    
            // Capa Satelital Híbrida de Google (Satelital + Calles)
            L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(this.map);
    
            setTimeout(() => this.map.invalidateSize(), 200);
    
            this.map.on('moveend', () => {
                let center = this.map.getCenter();
    
                this.$wire.updateLocation(center.lat, center.lng);
    
                if (this.autocomplete && typeof google !== 'undefined') {
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
                }, { enableHighAccuracy: true }
            );
        },
    
        initGooglePlaces() {
            const input = document.getElementById('google-places-input');
            if (!input) return;
    
            const checkGoogle = () => {
                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault() });
    
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
>
    <!-- Map Container (Absolute Full Screen) -->
    <div
        class="absolute inset-0 z-0 h-full w-full"
        wire:ignore
    >
        <div
            class="h-full w-full"
            id="map"
        ></div>

        <!-- Center Pin -->
        <div
            class="pointer-events-none absolute left-1/2 top-1/2 z-[1000] flex -translate-x-1/2 -translate-y-[90%] flex-col items-center">
            <div class="group relative">
                <div
                    class="absolute -bottom-1 left-1/2 h-1.5 w-4 -translate-x-1/2 rounded-[100%] bg-black/20 blur-[1px]">
                </div>
                <x-ui.map-pin />
            </div>
        </div>

        <!-- Locate Me Button -->
        <button
            class="absolute bottom-44 right-4 z-[1000] flex h-14 w-14 items-center justify-center rounded-2xl border border-gray-100 bg-white text-red-600 shadow-2xl transition-all active:scale-90"
            x-on:click="locateMe()"
        >
            <template x-if="!loadingLocation">
                <i class="bxf bx-location-pin text-2xl"></i>
            </template>
            <template x-if="loadingLocation">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-orange-100 border-t-orange-600"></div>
            </template>
        </button>
    </div>

    <!-- Top Search Input Bar -->
    <div class="pointer-events-none absolute left-0 right-0 top-6 z-50 p-4">
        <div class="mx-auto flex max-w-md flex-col gap-3">
            <div class="pointer-events-auto flex items-center gap-2">
                <button
                    class="flex h-12 w-12 cursor-pointer items-center justify-center rounded-2xl border border-gray-100 bg-white text-gray-800 transition-all active:scale-95"
                    onclick="history.back();"
                >
                    <i class="bxf bx-chevron-left text-3xl"></i>
                </button>

                <div
                    class="relative flex-1"
                    wire:ignore
                >
                    <input
                        class="h-12 w-full rounded-2xl border-none bg-white p-3 pl-11 text-sm text-gray-700 shadow-xl focus:ring-2 focus:ring-red-600"
                        id="google-places-input"
                        type="text"
                        placeholder="¿A dónde enviamos?"
                    />
                    <i class="bxf bx-search absolute left-4 top-3.5 text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Selected Address Card -->
    <div
        class="absolute bottom-0 left-0 right-0 z-40 flex flex-col gap-4 rounded-t-3xl bg-white p-4 shadow-[0_-10px_30px_rgba(0,0,0,0.08)]">
        <div class="flex items-start gap-3 p-2">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                <i class="bxf bx-location-plus text-xl text-red-500"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Dirección seleccionada</p>
                <p class="mt-0.5 line-clamp-2 text-sm font-semibold leading-snug text-gray-800">
                    @if ($formattedAddress)
                        {{ $formattedAddress }}
                    @else
                        Mueve el mapa para seleccionar...
                    @endif
                </p>
            </div>
        </div>

        <x-ui.button
            wire:click="save"
            :disabled="!$formattedAddress"
        >
            {{ $mode === 'checkout' ? 'Confirmar Ubicación' : 'Guardar ubicación' }}
        </x-ui.button>
    </div>
</div>