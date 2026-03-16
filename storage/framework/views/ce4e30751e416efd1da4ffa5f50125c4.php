<?php
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DeliveryAddress;
?>

<div class="h-screen flex flex-col bg-gray-50">

    
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

    
    <div id="map" wire:ignore class="flex-1 z-0 relative">
        
        <div wire:loading wire:target="updateLocation" class="absolute top-4 left-1/2 -translate-x-1/2 bg-black/70 text-white text-xs px-3 py-1 rounded-full z-[1000]">
            Buscando calle...
        </div>
    </div>

    
    <div class="p-4 bg-white rounded-t-3xl shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-10 flex flex-col gap-4">

        <div class="flex items-start gap-3 p-2">
            <i class="bxf bx-map text-red-500 text-2xl mt-1"></i>
            <div>
                <p class="text-xs text-gray-500 font-medium">Dirección seleccionada</p>
                <p class="text-sm font-semibold text-gray-800 line-clamp-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formattedAddress): ?>
                        <?php echo e($formattedAddress); ?>

                    <?php else: ?>
                        Mueve el pin en el mapa...
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>

        
        <button
            wire:click="save"
            
            <?php if(!$formattedAddress): ?> disabled <?php endif; ?>
            class="block w-full text-center bg-red-500 text-white font-bold p-4 rounded-2xl hover:bg-red-600 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100"
        >
            <?php echo e($mode === 'checkout' ? 'Siguiente' : 'Guardar dirección'); ?>

        </button>

    </div>

    
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
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/c06d6d6a.blade.php ENDPATH**/ ?>