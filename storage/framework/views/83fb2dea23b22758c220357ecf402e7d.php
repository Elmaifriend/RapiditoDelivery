<?php
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DeliveryAddress;
?>

<div 
    wire:navigate
    href="/location"
    class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4 cursor-pointer shadow-sm relative"
>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debugLat && $debugLng): ?>
        <div class="absolute top-1 right-2 text-[9px] font-mono text-gray-400 bg-gray-50 px-1 rounded border border-gray-100">
            LAT: <?php echo e($debugLat); ?> | LNG: <?php echo e($debugLng); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div>
        <div class="flex items-center gap-1 text-3xl mt-2">
            <i class="bxf bx-carrot text-red-500"></i>
            <h1 class="font-display font-extrabold text-gray-800">Rapidito</h1>
        </div>

        <div class="flex items-start gap-1.5 text-gray-500 mt-2">
            <i class="bxf bx-location text-red-400 mt-0.5"></i>
            
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-bold text-gray-800"><?php echo e($cityText); ?></span>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($streetText): ?>
                    <span class="max-w-[220px] truncate text-xs font-medium text-gray-500">
                        <?php echo e($streetText); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <i class="bxf bx-chevron-down text-xs mt-1 ml-1"></i>
        </div>
    </div>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/00532918.blade.php ENDPATH**/ ?>