<?php
use Livewire\Component;
?>

<div
    wire:click="select"
    class="group flex cursor-pointer select-none flex-col items-center justify-start gap-2 transition-transform active:scale-90"
>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'rounded-2xl border px-4 py-3 text-2xl transition-all',
        'border-red-100 bg-red-50 shadow-sm' => $active,
        'border-gray-100 bg-white' => !$active,
    ]); ?>">
        <span><?php echo e($icon); ?></span>
    </div>
    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'text-center text-xs font-bold transition-colors',
        'text-red-500' => $active,
        'text-gray-600' => !$active,
    ]); ?>">
        <?php echo e($category); ?>

    </span>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/7b39c369.blade.php ENDPATH**/ ?>