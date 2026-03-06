<?php
use Livewire\Component;
?>

<div>

    <div class="relative flex w-full items-center justify-center">
        <img class="h-46 w-full object-cover object-center" src="<?php echo e($image); ?>"/>
        <div class="absolute right-4 top-4 flex items-center gap-1 rounded-xl bg-white/80 px-2 py-1 text-xs font-bold">
            <i class="bxf bx-clock text-red-400"></i>
            <span><?php echo e($time); ?></span>
        </div>
    </div>

    <div class="flex flex-col gap-1 px-6 py-4">
        <div class="flex justify-between">
            <p class="font-bold"><?php echo e($name); ?></p>
            <div class="flex items-center gap-1 rounded-2xl bg-green-50 px-2 py-1 text-xs font-bold text-green-700">
                <span><?php echo e($stars); ?></span>
                <i class="bxf bx-star"></i>
            </div>
        </div>
        <span
            class="self-start rounded-xl bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500"><?php echo e($type); ?></span>
    </div>

</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/7b009a11.blade.php ENDPATH**/ ?>