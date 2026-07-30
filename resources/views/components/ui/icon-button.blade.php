@props(['icon', 'href' => null])

@if($href)
    <a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => 'shadow-xs flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-100 bg-white text-gray-800 transition-all active:scale-95']) }}>
        <i class="{{ $icon }} text-2xl"></i>
    </a>
@else
    <button {{ $attributes->merge(['class' => 'shadow-xs flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-100 bg-white text-gray-800 transition-all active:scale-95 cursor-pointer']) }}>
        <i class="{{ $icon }} text-2xl"></i>
    </button>
@endif
