<div class="flex flex-col gap-4 pt-6">
    <div class="flex gap-4 items-center px-4">
        <a
            class="shadow-xs flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-100 bg-white text-gray-800 transition-all active:scale-95"
            href="{{ url()->previous() ?? '/' }}"
            wire:navigate
        >
            <i class="bxf bx-chevron-left text-2xl"></i>
        </a>
        <h2 class="text-lg font-bold tracking-tight text-gray-900">{{ $tag->name }}</h2>
    </div>

    <div class="flex w-full flex-col gap-2 px-4">
        <div class="flex flex-col gap-4">
            @forelse($this->businesses as $restaurant)
                <x-ui.restaurant-card
                    :key="'res-'.$restaurant->id"
                    :business="$restaurant"
                    :name="$restaurant->name"
                    :type="$restaurant->category?->name ?? 'General'"
                    :stars="4.0"
                    time="30-40min"
                    :image="$restaurant->banner_path
                            ? Storage::temporaryUrl($restaurant->banner_path, now()->addMinutes(10))
                            : 'https://picsum.photos/300/200'"
                />
            @empty
                <div class="flex flex-col items-center py-24 text-center">
                    <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                    <p class="mt-2 text-sm text-gray-400 text-balance">
                        No encontramos restaurantes con el tag "{{ $tag->name }}" en tu zona.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
