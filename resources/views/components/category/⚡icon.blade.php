<?php

use Livewire\Component;

new class extends Component {
    public ?int $id = null;
    public string $icon;
    public string $category;
    public bool $active = false;

    public function select()
    {
        $this->dispatch('category-selected', categoryId: $this->id);
    }
};
?>

<div
    wire:click="select"
    class="group flex cursor-pointer select-none flex-col items-center justify-start gap-2 transition-transform active:scale-90"
>
    <div @class([
        'rounded-2xl border px-4 py-3 text-2xl transition-all',
        'border-red-100 bg-red-50 shadow-sm' => $active,
        'border-gray-100 bg-white' => !$active,
    ])>
        <span>{{ $icon }}</span>
    </div>
    <span @class([
        'text-center text-xs font-bold transition-colors',
        'text-red-500' => $active,
        'text-gray-600' => !$active,
    ])>
        {{ $category }}
    </span>
</div>
