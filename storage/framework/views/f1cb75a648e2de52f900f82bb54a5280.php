<?php
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DeliveryAddress;
?>

<div
    class="fixed top-0 z-100 flex gap-4 w-full items-center justify-between rounded-b-2xl bg-white px-6 pt-8 pb-6 cursor-pointer">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debugLat && $debugLng): ?>
    <div
        class="absolute top-1 right-1/2 translate-x-1/2 text-[9px] font-mono text-gray-400 bg-gray-50 px-1 rounded border border-gray-100">
        LAT: <?php echo e($debugLat); ?> | LNG: <?php echo e($debugLng); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <a class="flex items-center gap-1 text-3xl" wire:navigate href="/">
        <i class="bxf bx-carrot text-red-500"></i>
        <h1 class="font-display font-extrabold text-gray-800">Rapidito</h1>
    </a>

    <a class="flex items-end flex-col gap-1 text-gray-500 max-w-1/2" wire:navigate href="/location">
        <span class="text-xl flex items-center gap-1 justify-end font-bold text-gray-800">
            <?php echo e($cityText); ?>

            <i class="bxf bx-location text-red-400"></i>
        </span>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($streetText): ?>
        <div class="flex items-center justify-end gap-1 w-full text-gray-400">
            <p class="truncate text-xs text-right">
                <?php echo e($streetText); ?>

            </p>
            <i class="bxf bx-chevron-down shrink-0"></i>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/1cb836b0.blade.php ENDPATH**/ ?>