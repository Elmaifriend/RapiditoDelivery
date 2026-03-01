<?php

use Livewire\Component;

new class extends Component {
    public string $route;
    public string $icon;
    public string $label;
};
?>

<a href="{{ route($route) }}" wire:navigate
    class="data-current:text-red-500 data-current:font-bold flex w-16 flex-col items-center justify-center gap-1 p-2 text-gray-400 transition-all active:text-red-500 active:scale-90">
    <i class="{{ $icon }} text-[28px]"></i>
    <span class="text-xs font-medium">
        {{ $label }}
    </span>
</a>
