@props(['quantity', 'name', 'price', 'notes' => null])

<div {{ $attributes->merge(['class' => 'py-2 flex justify-between items-center text-xs']) }}>
    <span class="flex flex-col gap-1.5 text-gray-800 font-semibold">
        <div>
            <strong class="font-bold text-gray-900 bg-gray-200/50 px-1.5 py-0.5 rounded mr-1.5">{{ $quantity }}x</strong> 
            {{ $name }}
        </div>
        @if($notes)
            <span class="block text-xs font-medium text-gray-400">{{ $notes }}</span>
        @endif
    </span>
    <span class="font-bold text-gray-700 font-mono">${{ number_format($price, 2) }}</span>
</div>
