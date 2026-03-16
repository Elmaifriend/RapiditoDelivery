<?php
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;
?>

<div class="flex flex-col gap-4 p-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
            
            <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
                <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                    Pedido <?php echo e($index + 1); ?> de <?php echo e($this->carts->count()); ?>

                </span>
                <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                    <span><?php echo e($cart->business->delivery_time); ?> min</span>
                    <i class="fas fa-motorcycle"></i>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold leading-tight text-gray-800"><?php echo e($cart->business->name); ?></h3>
                </div>

                
                <div class="mb-4 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                                    <?php echo e($item->quantity); ?>x
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700"><?php echo e($item->product_name_snapshot); ?></p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-800">$<?php echo e(number_format($item->price_snapshot, 2)); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal comida</span>
                        <span>$<?php echo e(number_format($cart->subtotal, 2)); ?></span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800">
                        <span class="flex items-center gap-1">Envío</span>
                        <span>$<?php echo e(number_format($cart->delivery_fee, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="py-20 text-center">
            <p class="text-gray-400">Tu carrito está vacío</p>
            <a href="<?php echo e(route('home')); ?>" wire:navigate class="mt-4 inline-block font-bold text-red-500">Ir a comer</a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->carts->isNotEmpty()): ?>
        <div class="rounded-4xl bg-white p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Resumen de Pagos</h3>
            <div class="mb-2 flex justify-between px-2.5 text-sm text-gray-600">
                <span>Comida (<?php echo e($this->carts->count()); ?> Rest.)</span>
                <span>$<?php echo e(number_format($this->totals['subtotal'], 2)); ?></span>
            </div>
            <div class="mb-2 flex justify-between rounded-xl border border-red-100 bg-red-50 p-2.5 text-sm font-bold text-red-600">
                <span>Envíos</span>
                <span>$<?php echo e(number_format($this->totals['delivery'], 2)); ?></span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-4 text-2xl font-bold text-gray-900">
                <span>Total Final</span>
                <span>$<?php echo e(number_format($this->totals['total'], 2)); ?> MXN</span>
            </div>
        </div>

        <a href="<?php echo e(route('location', ['mode' => 'checkout'])); ?>" wire:navigate
            class="w-full rounded-2xl bg-red-500 px-6 py-4 text-center font-bold text-white transition-all active:scale-90">
            Continuar
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/56a74c0d.blade.php ENDPATH**/ ?>