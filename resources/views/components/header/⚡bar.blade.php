<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4">
    <div>
        <div class="flex items-center gap-1 text-3xl">
            <i class="bxf bx-carrot text-red-500"></i>
            <h1 class="font-display font-extrabold">Rapidito</h1>
        </div>
        <div class="flex items-center gap-1 text-gray-500">
            <p class="flex items-center gap-0.5 text-xs">
                <i class="bxf bx-location"></i>
                Casa - Calle 10 #20 Tijuana
            </p>
            <i class="bxf bx-chevron-down text-xs"></i>
        </div>
    </div>

    <div
        class="relative flex items-center justify-center rounded-2xl bg-red-50 p-3 text-2xl text-red-400 transition-all active:scale-90 active:bg-red-50/80 active:text-red-300">
        <i class="bxf bx-shopping-bag-alt"></i>
        <div class="absolute -right-0.5 -top-0.5 flex size-4 items-center justify-center rounded-full bg-red-500">
            <span class="text-[10px] font-bold text-white">4</span>
        </div>
    </div>
</div>
