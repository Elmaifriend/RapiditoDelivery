@props([
    'business',
    'name',
    'stars',
    'type',
    'time',
    'image'
])

<a wire:navigate href="{{ route('business', $business) }}"
    class="rounded-4xl overflow-hidden bg-white transition-all active:scale-[0.98] block border border-gray-150 shadow-xs">

    <div class="relative flex w-full items-center justify-center">
        <img class="h-46 w-full object-cover object-center" src="{{ $image }}" alt="{{ $name }}" />

        <div class="absolute right-4 top-4 flex items-center gap-1 rounded-xl bg-white/80 px-2 py-1 text-xs font-bold">
            <i class="bxf bx-clock text-red-400"></i>
            <span>{{ $time }}</span>
        </div>
    </div>

    <div class="flex flex-col gap-1 px-6 py-4">
        <div class="flex justify-between">
            <p class="font-bold text-gray-800">{{ $name }}</p>
            <div class="flex items-center gap-1 rounded-2xl bg-green-50 px-2 py-1 text-xs font-bold text-green-700">
                <span>{{ number_format($stars, 1) }}</span>
                <i class="bxf bx-star"></i>
            </div>
        </div>
        <span class="self-start rounded-xl bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">
            {{ $type }}
        </span>
    </div>
</a>
