@props([
    'business',
    'name',
    'type',
    'image',
    'isOpen' => true,
])

<div class="relative w-full">
    <a
        href="{{ $isOpen ? route('business', $business->id) : '#' }}"
        @if($isOpen) wire:navigate @endif
        @class([
            'group block w-full overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all shadow-sm',
            'hover:shadow-md active:scale-[0.99]' => $isOpen,
            'pointer-events-none cursor-not-allowed opacity-60 grayscale' => !$isOpen,
        ])
    >
        {{-- Imagen del restaurante y Badge de estado --}}
        <div class="relative h-36 w-full overflow-hidden bg-gray-100">
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
            
            @if(!$isOpen)
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-[1px]">
                    <span class="rounded-full bg-gray-900/90 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-md">
                        Cerrado
                    </span>
                </div>
            @endif
        </div>

        {{-- Información --}}
        <div class="flex items-center justify-between p-4">
            <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                <h3 class="truncate font-bold text-gray-800 group-hover:text-red-500">
                    {{ $name }}
                </h3>
                <span class="text-xs font-medium text-gray-400">
                    {{ $type }}
                </span>
            </div>

            @if($isOpen)
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 text-gray-400 transition-colors group-hover:bg-red-50 group-hover:text-red-500">
                    <i class="bx bx-chevron-right text-xl"></i>
                </span>
            @endif
        </div>
    </a>
</div>