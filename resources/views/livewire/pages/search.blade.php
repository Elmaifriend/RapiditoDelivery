@extends('layouts.page')

@section('content')
    <x-ui.page-section>
        <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white pl-4 shadow-sm">
            <i class="bxf bx-search text-lg text-red-400"></i>
            <input
                class="w-full border-none py-4 pr-4 text-sm font-medium text-gray-800 focus:ring-0"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="¿Qué se te antoja hoy?"
            >
        </div>
    </x-ui.page-section>

    <x-ui.page-section title="Top Categorías">
        <div class="grid grid-cols-2 gap-4">
            @if ($tag = $this->topTags->firstWhere('name', 'Hamburguesas'))
                <a
                    class="relative flex h-28 flex-col justify-center overflow-hidden rounded-3xl border border-orange-200/60 bg-orange-50 p-5 shadow-sm transition-all active:scale-[0.98]"
                    href="/tag/{{ $tag->id }}"
                    wire:navigate
                >
                    <span class="relative z-10 text-base font-bold leading-tight text-orange-900">Comida<br>Rápida</span>
                    <span class="absolute -bottom-1 -right-1 text-6xl drop-shadow-sm">🍔</span>
                </a>
            @endif

            @if ($tag = $this->topTags->firstWhere('name', 'Vegano'))
                <a
                    class="relative flex h-28 flex-col justify-center overflow-hidden rounded-3xl border border-emerald-200/60 bg-emerald-50 p-5 shadow-sm transition-all active:scale-[0.98]"
                    href="/tag/{{ $tag->id }}"
                    wire:navigate
                >
                    <span class="relative z-10 text-base font-bold leading-tight text-emerald-900">Saludable<br>& Fit</span>
                    <span class="absolute -bottom-1 -right-1 text-6xl drop-shadow-sm">🥗</span>
                </a>
            @endif

            @if ($tag = $this->topTags->firstWhere('name', 'Café'))
                <a
                    class="relative flex h-28 flex-col justify-center overflow-hidden rounded-3xl border border-amber-200/60 bg-amber-50 p-5 shadow-sm transition-all active:scale-[0.98]"
                    href="/tag/{{ $tag->id }}"
                    wire:navigate
                >
                    <span class="relative z-10 text-base font-bold leading-tight text-amber-900">Bebidas<br>& Café</span>
                    <span class="absolute -bottom-1 -right-1 text-6xl drop-shadow-sm">☕</span>
                </a>
            @endif

            @if ($tag = $this->topTags->firstWhere('name', 'Postres'))
                <a
                    class="relative flex h-28 flex-col justify-center overflow-hidden rounded-3xl border border-rose-200/60 bg-rose-50 p-5 shadow-sm transition-all active:scale-[0.98]"
                    href="/tag/{{ $tag->id }}"
                    wire:navigate
                >
                    <span class="relative z-10 text-base font-bold leading-tight text-rose-900">Postres<br>& Dulces</span>
                    <span class="absolute -bottom-1 -right-1 text-6xl drop-shadow-sm">🍰</span>
                </a>
            @endif
        </div>
    </x-ui.page-section>

    <x-ui.page-section>
        <div
            class="relative w-full py-4"
            wire:loading
            wire:target="search"
        >
            <i class="bxf bx-loader-lines-alt -translate-1/2 absolute left-1/2 animate-spin text-4xl text-red-500"></i>
        </div>
    </x-ui.page-section>

    @if (strlen($search) >= 2)
        <x-ui.page-section
            wire:loading.remove
            wire:target="search"
        >
            <h2 class="font-bold text-gray-800">Resultados para "{{ $search }}"</h2>
            <div class="flex flex-col gap-4">
                @forelse($this->businesses as $restaurant)
                    <x-ui.restaurant-card
                        :key="'search-res-' . $restaurant->id"
                        :business="$restaurant"
                        :name="$restaurant->name"
                        :type="$restaurant->category?->name ?? 'General'"
                        :image="$restaurant->banner_path
                            ? Storage::temporaryUrl($restaurant->banner_path, now()->addMinutes(10))
                            : 'https://picsum.photos/300/200'"
                    />
                @empty
                    <div class="flex flex-col items-center py-10 text-center">
                        <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                        <p class="mt-2 text-sm text-gray-400">No encontramos resultados para tu búsqueda.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.page-section>
    @endif
@endsection
