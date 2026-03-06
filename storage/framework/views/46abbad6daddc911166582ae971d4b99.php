<?php
use Livewire\Component;
?>

<a href="<?php echo e(route($route)); ?>" wire:navigate
    class="data-current:text-red-500 data-current:font-bold flex w-16 flex-col items-center justify-center gap-1 p-2 text-gray-400 transition-all active:text-red-500 active:scale-90">
    <i class="<?php echo e($icon); ?> text-[28px]"></i>
    <span class="text-xs font-medium">
        <?php echo e($label); ?>

    </span>
</a><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/8751c304.blade.php ENDPATH**/ ?>