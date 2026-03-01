<?php

use Livewire\Component;

new class extends Component {
    public string $icon;
    public string $category;
};
?>

<div class="group flex select-none flex-col items-center justify-start gap-2 transition-transform active:scale-90">
    <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3 text-2xl">
        <span>{{ $icon }}</span>
    </div>
    <span
        class="text-center text-xs font-medium text-gray-600 transition-colors group-active:text-red-500">{{ $category }}</span>
</div>
