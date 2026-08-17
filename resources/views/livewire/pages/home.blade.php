@extends('layouts.page')

@section('content')
    <div
        class="flex flex-col gap-8 h-full"
        x-data="{
            showToast: false,
            toastTimer: null,
            triggerToast() {
                clearTimeout(this.toastTimer);
                this.showToast = true;
                this.toastTimer = setTimeout(() => { this.showToast = false; }, 5000);
            }
        }"
    >
        {{-- No hay cobertura --}}
        @if($noService)
            <div class="flex flex-col items-center justify-center gap-8 px-6 py-24 text-center">
                <span class="text-6xl text-red-500">:(</span>
                <h2 class="text-2xl font-bold tracking-tight text-gray-800">
                    ¡Vaya! Aún no llegamos ahí
                </h2>
                <p class="max-w-xs text-sm leading-relaxed text-gray-500">
                    Lo sentimos mucho, pero aún no tenemos cobertura en tu ubicación actual.
                </p>
                <div class="flex flex-col gap-4">
                    <p class="text-sm font-medium text-gray-400">
                        ¿Crees que es un error?
                    </p>
                    <a
                        class="block w-full rounded-2xl bg-red-500 p-4 text-center font-bold text-white shadow-md shadow-red-200 transition-all hover:bg-red-600 active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100"
                        href="/location"
                        wire:navigate
                    >
                        Seleccionar ubicación manualmente
                    </a>
                    <p class="text-sm italic text-gray-400">
                        A veces el GPS tiene hambre y se confunde un poco
                    </p>
                </div>
            </div>

        {{-- Estado de ubicación denegada --}}
        @elseif ($locationDenied)
            <div class="flex flex-col justify-center gap-8 px-6 py-12 text-center">
                <span class="text-6xl text-red-500">
                    <i class="bxf bx-lock"></i>
                </span>
                <h2 class="text-2xl font-bold tracking-tight text-gray-800">
                    Necesitamos acceso a tu ubicación
                </h2>
                <p class="max-w-xs mx-auto text-sm leading-relaxed text-gray-500">
                    No pudimos acceder a tu ubicación. Necesitamos saber dónde estás para mostrarte los mejores sabores a tu
                    alrededor.
                </p>
                <div class="flex flex-col gap-4">
                    <p class="text-sm font-medium text-gray-400">
                        ¿Prefieres hacerlo tú mismo?
                    </p>
                    <a
                        class="block w-full rounded-2xl bg-red-500 p-4 text-center font-bold text-white shadow-md shadow-red-200 transition-all hover:bg-red-600 active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100"
                        href="/location"
                        wire:navigate
                    >
                        Seleccionar ubicación manualmente
                    </a>
                    <p class="text-sm italic text-gray-400">
                        Prometemos no seguirte hasta tu cocina
                        <br/>
                        (solo hasta la puerta)
                    </p>
                </div>
            </div>

        {{-- Hay ciudad y cobertura --}}
        @elseif($city)
            <x-ui.page-section>
                <x-ui.sell-banner />
            </x-ui.page-section>

            <div class="flex w-full flex-col gap-2">
                <div class="flex items-center justify-between px-4">
                    <h2 class="font-bold text-gray-800">Explorar por categorías</h2>
                </div>

                <div
                    class="no-scrollbar grid auto-cols-[72px] grid-flow-col justify-items-center gap-4 overflow-x-auto overflow-y-hidden px-4">
                    @foreach ($this->tags as $tag)
                        @php
                            $categories = [
                                'Tacos' => ['icon' => '🌮', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200/50'],
                                'Hamburguesas' => [
                                    'icon' => '🍔',
                                    'bg' => 'bg-orange-50 text-orange-700 border-orange-200/50',
                                ],
                                'Pizza' => ['icon' => '🍕', 'bg' => 'bg-red-50 text-red-700 border-red-200/50'],
                                'Sushi' => ['icon' => '🍣', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200/50'],
                                'Mariscos' => ['icon' => '🍤', 'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200/50'],
                                'Vegano' => [
                                    'icon' => '🥗',
                                    'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
                                ],
                                'Postres' => ['icon' => '🍰', 'bg' => 'bg-pink-50 text-pink-700 border-pink-200/50'],
                                'Café' => ['icon' => '☕', 'bg' => 'bg-amber-100 text-amber-900 border-amber-200'],
                                'Alitas' => [
                                    'icon' => '🍗',
                                    'bg' => 'bg-yellow-50 text-yellow-800 border-yellow-200/50',
                                ],
                                'Desayunos' => ['icon' => '🍳', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200/50'],
                            ];
                            $category = $categories[$tag->name] ?? [
                                'icon' => '🍴',
                                'bg' => 'bg-gray-50 text-gray-700 border-gray-200/50',
                            ];
                        @endphp

                        <button
                            class="flex shrink-0 cursor-pointer flex-col items-center gap-2 transition-all active:scale-95"
                            type="button"
                            x-on:click="triggerToast()"
                        >
                            <div
                                class="{{ $category['bg'] }} font-fluent flex h-16 w-16 items-center justify-center rounded-2xl border text-4xl shadow-sm drop-shadow-sm">
                                {{ $category['icon'] }}
                            </div>
                            <span class="text-xs font-semibold text-gray-700">{{ $tag->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <x-ui.page-section title="Restaurantes cerca">
                @forelse($this->filteredBusinesses as $restaurant)
                    <x-ui.restaurant-card
                        :key="'res-' . $restaurant->id"
                        :business="$restaurant"
                        :name="$restaurant->name"
                        :type="$restaurant->category?->name ?? 'General'"
                        :isOpen="$restaurant->is_open"
                        :image="$restaurant->banner_path
                            ? Storage::temporaryUrl($restaurant->banner_path, now()->addMinutes(10))
                            : 'https://picsum.photos/300/200'"
                    />
                @empty
                    <div class="flex flex-col items-center py-10 text-center">
                        <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                        <p class="mt-2 text-balance text-sm text-gray-400">
                            No encontramos restaurantes en tu zona actualmente.
                        </p>
                    </div>
                @endforelse
            </x-ui.page-section>

            <x-ui.card
                class="pointer-events-none absolute bottom-24 left-1/2 z-50 flex w-[90%] max-w-sm -translate-x-1/2 items-start gap-3"
                style="display: none;"
                x-show="showToast"
                x-transition
            >
                <x-ui.map-pin />
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-bold text-red-500">¡Estamos creciendo!</p>
                    <p class="text-xs text-gray-500">
                        Pocos restaurantes en esta zona para filtrar. ¡Recomiéndanos para crecer más rápido!
                    </p>
                </div>
            </x-ui.card>
        @endif

        @script
            <script>
                document.addEventListener('livewire:navigated', () => {

                    const alreadyHasAddress = @js($hasAddress);

                    if (alreadyHasAddress) {
                        return;
                    }

                    if (!navigator.geolocation) {
                        console.warn('Geolocation no soportada.');
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            Livewire.dispatch('locationDetected', {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            });
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            Livewire.dispatch('locationDenied');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000,
                        }
                    );

                });
            </script>
        @endscript
    </div>
@endsection