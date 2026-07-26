<div
    class="z-100 flex gap-4 w-full items-center justify-between rounded-b-2xl bg-white px-6 pt-8 pb-6 cursor-pointer">
    {{-- ETIQUETA DE DEBUG: Se muestra chiquito arriba a la derecha --}}
    @if($debugLat && $debugLng)
    <div
        class="absolute top-1 right-1/2 translate-x-1/2 text-[9px] font-mono text-gray-400 bg-gray-50 px-1 rounded border border-gray-100">
        LAT: {{ $debugLat }} | LNG: {{ $debugLng }}
    </div>
    @endif

    <a class="flex items-center gap-1 text-3xl" wire:navigate href="/">
        <i class="bxf bx-carrot text-red-500"></i>
        <h1 class="font-display font-extrabold text-gray-800">Rapidito</h1>
    </a>

    <a class="flex items-end flex-col gap-1 text-gray-500 max-w-1/2" wire:navigate href="/location">
        <span class="text-lg text-end flex items-center gap-1 justify-end font-semibold text-gray-800">
            {{ $cityText }}
            <i class="bxf bx-location text-red-400"></i>
        </span>

        @if($streetText)
        <div class="flex items-center justify-end gap-1 w-full text-gray-400">
            <p class="truncate text-xs text-right font-semibold">
                {{ $streetText }}
            </p>
            <i class="bxf bx-chevron-down shrink-0"></i>
        </div>
        @endif
    </a>
</div>
