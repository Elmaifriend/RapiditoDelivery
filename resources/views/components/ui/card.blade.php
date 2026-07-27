@props(['padding' => 'p-5', 'border' => 'border-gray-200', 'bg' => 'bg-white', 'rounded' => 'rounded-3xl'])

<div {{ $attributes->merge(['class' => "shadow-sm {$rounded} border {$border} {$bg} {$padding} overflow-hidden"]) }}>
    {{ $slot }}
</div>
