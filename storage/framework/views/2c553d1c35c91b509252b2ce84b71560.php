<?php
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\ServiceZone;
use App\Models\DeliveryAddress;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
?>

<div class="flex flex-col gap-4 pt-4">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationDenied): ?>
    <div class="flex flex-col items-center justify-center px-6 py-24 gap-8 text-center">
        <span class="text-6xl text-red-500">
            <i class="bxf bx-lock-alt"></i>
        </span>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
            Necesitamos acceso a tu ubicación
        </h2>
        <p class="max-w-xs text-sm leading-relaxed text-gray-500">
            No pudimos acceder a tu ubicación. Necesitamos saber dónde estás para mostrarte los mejores sabores a tu alrededor.
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
    
    
    <?php elseif($city): ?>
    <div class="flex w-full flex-col gap-2">
        <div class="flex items-center justify-between px-4">
            <h2 class="font-bold text-gray-800">Categorías</h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCategoryId): ?>
            <button wire:click="filterByCategory(null)" class="text-xs font-bold text-red-500">Limpiar</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="no-scrollbar flex gap-4 overflow-x-auto px-4 pb-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = App\Models\Category::active()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('category.icon', ['id' => $cat->id,'category' => $cat->name,'icon' => '🍴','active' => $selectedCategoryId === $cat->id]);

$__keyOuter = $__key ?? null;

$__key = 'cat-'.$cat->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-555845287-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <div class="flex w-full flex-col gap-2 px-4">
        <h2 class="font-bold text-gray-800">
            <?php echo e($selectedCategoryId ? 'Resultados' : 'Restaurantes cerca'); ?>

        </h2>

        <div class="flex flex-col gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->filteredBusinesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('restaurant.card', ['business' => $restaurant,'name' => $restaurant->name,'type' => $restaurant->category?->name ?? 'General','stars' => 4.0,'time' => '30-40min','image' => $restaurant->banner_path
                            ? Storage::temporaryUrl($restaurant->banner_path, now()->addMinutes(10))
                            : 'https://picsum.photos/300/200']);

$__keyOuter = $__key ?? null;

$__key = 'res-'.$restaurant->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-555845287-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="flex flex-col items-center py-10 text-center">
                    <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                    <p class="mt-2 text-sm text-gray-400 text-balance">
                        No encontramos restaurantes de esta categoría en tu zona.
                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php elseif($noService): ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script>
        document.addEventListener('livewire:navigated', () => {
            // Evaluamos la propiedad del componente directamente de forma limpia, sin usar Eloquent aquí
            const alreadyHasAddress = <?php echo e($hasAddress ? 'true' : 'false'); ?>;

            if (alreadyHasAddress) {
                return;
            }

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
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/aa983371.blade.php ENDPATH**/ ?>