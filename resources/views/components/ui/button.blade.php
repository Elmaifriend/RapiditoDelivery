@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $baseClass =
        'flex items-center justify-center py-4 px-3 gap-2 rounded-2xl text-sm shadow-md font-bold transition-all active:scale-[0.98] cursor-pointer disabled:opacity-40 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-red-500 hover:bg-red-600 text-white shadow-red-200/50',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs',
        'dark' => 'bg-gray-900 hover:bg-gray-800 text-white shadow-md',
        'outline' => 'bg-white hover:bg-red-50 text-red-500 border border-gray-200 hover:border-red-200',
        'secondary' => 'bg-red-50 text-red-500 hover:text-red-600',
    ];

    $class = $baseClass . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        wire:navigate
        {{ $attributes->merge(['class' => $class]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
    >
        {{ $slot }}
    </button>
@endif
