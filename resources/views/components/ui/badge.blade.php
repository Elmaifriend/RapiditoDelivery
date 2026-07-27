@props(['color' => 'red', 'pulse' => null])

@php
    $colors = [
        'red' => 'bg-red-50 border-red-100 text-red-500 dot:bg-red-500',
        'green' => 'bg-green-50 border-green-100 text-green-500 dot:bg-green-500',
        'amber' => 'bg-amber-50 border-amber-200 text-amber-800 dot:bg-amber-500',
        'blue' => 'bg-blue-50 border-blue-200 text-blue-800 dot:bg-blue-500',
        'indigo' => 'bg-indigo-50 border-indigo-200 text-indigo-700 dot:bg-indigo-500',
        'gray' => 'bg-gray-50 border-gray-200 text-gray-600 dot:bg-gray-400',
        'transparent' => 'border-white/10 bg-white/20 backdrop-blur-sm',
    ];
    $theme = $colors[$color] ?? $colors['red'];

    $dotColor = 'bg-red-500';
    if ($color === 'green') {
        $dotColor = 'bg-green-500';
    }
    if ($color === 'amber') {
        $dotColor = 'bg-amber-500';
    }
    if ($color === 'blue') {
        $dotColor = 'bg-blue-500';
    }
    if ($color === 'indigo') {
        $dotColor = 'bg-indigo-500';
    }
    if ($color === 'gray') {
        $dotColor = 'bg-gray-500';
    }
    if ($color === 'transparent') {
        $dotColor = 'bg-white';
    }
@endphp

<span
    {{ $attributes->merge(['class' => "text-ellipsis text-center inline-flex items-center gap-2 border text-xs font-medium px-4 py-1.5 rounded-full {$theme}"]) }}
>
    @isset($pulse)
        <span class="{{ $dotColor }} shrink-0 h-1.5 w-1.5 animate-pulse rounded-full"></span>
    @endisset
    {{ $slot }}
</span>
