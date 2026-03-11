<?php
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DeliveryAddress;
?>

<div 
    wire:navigate
    href="/location"
    class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4 cursor-pointer shadow-sm"
>
    <div>
        <div class="flex items-center gap-1 text-3xl">
            <i class="bxf bx-carrot text-red-500"></i>
            <h1 class="font-display font-extrabold">Rapidito</h1>
        </div>

        <div class="flex items-center gap-1 text-gray-500 mt-1">
            <p class="flex items-center gap-1 text-xs font-medium">
                <i class="bxf bx-location text-red-400"></i>
                
                
                <span class="truncate max-w-[200px]"><?php echo e($locationText); ?></span>
                
            </p>
            <i class="bxf bx-chevron-down text-xs"></i>
        </div>
    </div>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/00532918.blade.php ENDPATH**/ ?>