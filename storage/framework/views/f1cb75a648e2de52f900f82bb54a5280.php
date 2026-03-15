<?php
use Livewire\Component;
?>

<div class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('header.location', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2345179150-0', $__key);

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

    <div
        class="relative flex items-center justify-center rounded-2xl bg-red-50 p-3 text-2xl text-red-400 transition-all active:scale-90 active:bg-red-50/80 active:text-red-300">
        <i class="bxf bx-shopping-bag-alt"></i>
        <div class="absolute -right-0.5 -top-0.5 flex size-4 items-center justify-center rounded-full bg-red-500">
            <span class="text-[10px] font-bold text-white">4</span>
        </div>
    </div>
</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/1cb836b0.blade.php ENDPATH**/ ?>