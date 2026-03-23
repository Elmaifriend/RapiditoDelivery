<?php
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Business;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;
?>

<div class="min-h-screen bg-gray-50 pb-28">
    <div class="relative h-52 w-full overflow-hidden bg-gray-200">
        <img src="<?php echo e($business->banner_path ? Storage::temporaryUrl($business->banner_path, now()->addMinutes(10)) : 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop'); ?>"
            class="h-full w-full object-cover">
    </div>

    <div class="bg-white px-5 pb-5 pt-4">
        <h1 class="text-2xl font-bold text-gray-800">
            <?php echo e($business->name); ?>

        </h1>
        <p class="mt-1 text-sm text-gray-500">
            <?php echo e($business->category?->name); ?> • 25-35 min
        </p>
    </div>

    <div class="sticky top-0 z-10 bg-gray-50">
        <div class="no-scrollbar flex items-center gap-3 overflow-x-auto p-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $business->productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <a href="#category-<?php echo e($category->id); ?>"
                    class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 transition-transform active:scale-90">
                    <?php echo e($category->name); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
    <div class="space-y-8 px-4 pt-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $business->productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div id="category-<?php echo e($category->id); ?>">
                <h2 class="mb-3 text-lg font-bold text-gray-800">
                    <?php echo e($category->name); ?>

                </h2>
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $category->products->where('is_active', true)->where('is_available', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div wire:click="openProductModal(<?php echo e($product->id); ?>)"
                            class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-100 bg-white p-3 transition-all active:scale-[0.98]">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-bold text-gray-800"><?php echo e($product->name); ?></h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
                                    <p class="mt-1 line-clamp-2 text-xs text-gray-400"><?php echo e($product->description); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span
                                    class="mt-2 block font-bold text-gray-900">$<?php echo e(number_format($product->price, 2)); ?></span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-gray-200">
                                    <img src="<?php echo e($product->image_path ? Storage::url($product->image_path) : 'https://placehold.co/150x150'); ?>"
                                        class="h-full w-full object-cover">
                                </div>
                                <?php
                                    $itemInCart = $this->cart?->items->where('product_id', $product->id)->first();
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemInCart): ?>
                                    <div
                                        class="flex w-full items-center gap-2 rounded-lg bg-red-500 px-2 py-1 text-xs font-bold text-white shadow-sm">
                                        <span>-</span>
                                        <span class="w-full text-center"><?php echo e($itemInCart->quantity); ?></span>
                                        <span>+</span>
                                    </div>
                                <?php else: ?>
                                    <button
                                        class="w-full rounded-lg border border-red-200 px-4 py-1 text-xs font-bold text-red-500">
                                        Agregar
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <div x-data="{ open: <?php if ((object) ('showProductModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showProductModal'->value()); ?>')<?php echo e('showProductModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showProductModal'); ?>')<?php endif; ?> }" x-show="open" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
        x-cloak>
        <div x-show="open" x-transition.opacity x-on:click="open = false"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 sm:scale-100"
            class="relative w-full max-w-lg overflow-hidden rounded-t-3xl bg-white p-6 pb-28 shadow-xl sm:rounded-3xl">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
                <div class="relative h-64 w-full overflow-hidden rounded-2xl bg-gray-100">
                    <img src="<?php echo e($selectedProduct->image_path ? Storage::url($selectedProduct->image_path) : 'https://placehold.co/600x400'); ?>"
                        class="h-full w-full object-cover">
                    <button x-on:click="open = false"
                        class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-md">✕</button>
                </div>
                <div class="mt-4">
                    <h3 class="text-xl font-bold text-gray-800"><?php echo e($selectedProduct->name); ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-500"><?php echo e($selectedProduct->description); ?></p>
                    <span
                        class="mt-4 block text-lg font-black text-gray-900">$<?php echo e(number_format($selectedProduct->price, 2)); ?></span>
                </div>
                <div class="mt-8 flex items-center justify-between">
                    <div class="flex items-center gap-4 rounded-xl border border-gray-200 p-1">
                        <button wire:click="decrement"
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">-</button>
                        <span class="w-8 text-center font-bold text-gray-800"><?php echo e($quantity); ?></span>
                        <button wire:click="increment"
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">+</button>
                    </div>
                    <button wire:click="addToCart"
                        class="<?php echo e($quantity > 0 ? 'bg-red-500' : 'bg-gray-800'); ?> ml-4 flex-1 rounded-xl py-3 font-bold text-white shadow-lg transition-all active:scale-95">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantity > 0): ?>
                            Agregar
                        <?php else: ?>
                            Quitar del pedido
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div x-data="{ open: <?php if ((object) ('showStartNewCartModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showStartNewCartModal'->value()); ?>')<?php echo e('showStartNewCartModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showStartNewCartModal'); ?>')<?php endif; ?> }" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4"
        x-cloak>

        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">

            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.34c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900">¿Empezar un nuevo carrito?</h3>

            <p class="mt-2 text-sm text-gray-500">
                Ya tienes productos de otro negocio en tu carrito. Si continúas, se borrará tu pedido anterior para
                agregar este.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <button wire:click="clearCart"
                    class="w-full rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg transition-transform active:scale-95">
                    Nuevo pedido
                </button>

                <button x-on:click="open = false"
                    class="w-full py-2 text-sm font-bold text-gray-400 active:text-gray-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cart.bar', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2865290527-0', $__key);

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
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/46164ca5.blade.php ENDPATH**/ ?>