<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->cart && $this->cart->items->count()): ?>
<div class="fixed bottom-20 left-0 right-0 z-40 flex justify-center px-4">

    <div class="flex w-full max-w-[900px] items-center justify-between rounded-2xl bg-gray-900 px-5 py-4 text-white shadow-2xl">

        <div>
            <span class="text-xs text-gray-300">
                <?php echo e($this->cart->items->sum('quantity')); ?> productos
            </span>

            <div class="text-lg font-bold">
                $<?php echo e(number_format($this->cart->total, 2)); ?>

            </div>
        </div>

        <a href="<?php echo e(route("cart")); ?>" wire:navigate class="rounded-xl bg-red-500 px-6 py-2 font-bold active:scale-95">
            Comprar
        </a>

    </div>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\resources\views/livewire/cart/bar.blade.php ENDPATH**/ ?>