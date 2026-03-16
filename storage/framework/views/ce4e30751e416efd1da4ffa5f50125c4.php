<?php
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use App\Models\DeliveryAddress;
?>


<div x-data="{
        map: null,
        autocomplete: null,

        // init() arranca mágicamente en cuanto pisas esta página
        init() {
            this.initLeaflet();
            this.initGooglePlaces();
        },

        initLeaflet() {
            const container = document.getElementById('map');
            if(!container) return;

            // Limpieza obligatoria para que Leaflet no explote al ir y venir
            if(container._leaflet_id) {
                container._leaflet_id = null;
            }

            this.map = L.map('map', { zoomControl: false }).setView([32.5149, -117.0382], 16);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

            // Forzamos dibujo correcto
            setTimeout(() => this.map.invalidateSize(), 100);

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
    class="fixed inset-0 z-[100] h-[100dvh] flex flex-col bg-gray-50 overflow-hidden"
>

    
    <div class="p-4 bg-white shadow z-20 flex-shrink-0 flex flex-col gap-2 relative">
        <div class="flex items-center gap-2">
            <a wire:navigate href="/" class="p-2 text-gray-500 rounded-full hover:bg-gray-100">
                <i class="bxf bx-arrow-back text-xl"></i>
            </a>
            <h1 class="font-bold text-lg">Elige tu ubicación</h1>
        </div>

        <div class="relative" wire:ignore>
            <input 
                id="google-places-input"
                type="text"
                placeholder="Busca tu calle o colonia..."
                class="w-full border-2 border-gray-100 rounded-xl p-3 pl-10 bg-gray-50 focus:border-red-400 focus:outline-none focus:bg-white transition-colors"
            />
            <i class="bxf bx-search absolute left-3 top-3.5 text-xl text-gray-400"></i>
        </div>
    </div>

    
    <div class="flex-1 relative z-0" wire:ignore>
        <div id="map" class="absolute inset-0"></div>

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-full z-[1000] pointer-events-none mb-3 flex flex-col items-center">
            <div class="relative bg-white rounded-full p-2 shadow-lg border border-gray-100 flex items-center justify-center animate-bounce-slow">
                <i class="bxf bx-carrot text-4xl text-orange-500"></i>
                <div class="absolute -bottom-2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[8px] border-t-white"></div>
            </div>
            <div class="w-3 h-1.5 bg-black/20 rounded-[100%] mt-2 blur-[1px]"></div>
        </div>

        <div wire:loading wire:target="updateLocation" class="absolute top-4 left-1/2 -translate-x-1/2 bg-black/80 text-white font-medium text-xs px-4 py-2 rounded-full z-[1000] shadow-lg">
            Calculando dirección...
        </div>
    </div>

    
    <div class="p-4 bg-white rounded-t-3xl shadow-[0_-10px_30px_rgba(0,0,0,0.08)] z-10 flex-shrink-0 flex flex-col gap-4">

        <div class="flex items-start gap-3 p-2">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="bxf bx-location-plus text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Dirección seleccionada</p>
                <p class="text-sm font-semibold text-gray-800 line-clamp-2 mt-0.5 leading-snug">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formattedAddress): ?>
                        <?php echo e($formattedAddress); ?>

                    <?php else: ?>
                        Mueve el mapa para seleccionar...
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>

        <button
            wire:click="save"
            <?php if(!$formattedAddress): ?> disabled <?php endif; ?>
            class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100 shadow-md shadow-red-200"
        >
            <?php echo e($mode === 'checkout' ? 'Confirmar Ubicación' : 'Guardar ubicación'); ?>

        </button>

    </div>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/c06d6d6a.blade.php ENDPATH**/ ?>