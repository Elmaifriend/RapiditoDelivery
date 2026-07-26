<div
    class="flex flex-col gap-8 pt-3"
    x-data="{
        showToast: false,
        toastTimer: null,
        triggerToast() {
            clearTimeout(this.toastTimer);
            this.showToast = true;
            this.toastTimer = setTimeout(() => { this.showToast = false; }, 3000);
        }
    }"
>

    <div class="px-4">
        <div
            class="rounded-4xl bg-linear-to-tr relative overflow-hidden border border-white/20 from-orange-400 via-red-500 to-rose-600 p-6 text-white shadow-2xl shadow-rose-500/25">
            <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/25 blur-xl"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-yellow-400/25 blur-xl"></div>

            <div class="relative z-10 flex flex-col gap-4">
                <span
                    class="inline-flex items-center gap-1.5 self-start rounded-full border border-white/10 bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm"
                >
                    <i class="bxf bx-store-alt text-xs"></i> Unete a Rapidito
                </span>

                <div>
                    <h3 class="text-2xl font-extrabold leading-tight tracking-tight text-white drop-shadow-sm">
                        ¿Quieres vender más?
                    </h3>
                    <p class="mt-1 max-w-sm text-xs leading-relaxed text-white/90">
                        Conecta con miles de clientes locales y multiplica tus ventas de inmediato.
                    </p>
                </div>

                <a
                    class="group flex w-full select-none items-center justify-center gap-2 rounded-2xl bg-white py-3.5 text-center text-xs font-bold text-red-600 shadow-xl shadow-rose-950/10 transition-all active:scale-90 active:bg-rose-50"
                    href="https://wa.me/5216647921114?text=Hola!%20Quiero%20registrar%20mi%20negocio%20y%20aumentar%20mis%20ventas%20con%20Rapidito!"
                    wire:navigate
                >
                    <span>Registrar mi negocio</span>
                    <i class="bxf bx-right-arrow-alt text-base transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Notificación flotante discreta --}}
    <div
        class="pointer-events-none fixed bottom-20 left-1/2 z-50 flex w-[90%] max-w-sm -translate-x-1/2 items-start gap-3 rounded-2xl border border-gray-800 bg-gray-900/95 p-4 text-xs font-semibold text-white shadow-xl backdrop-blur-md"
        style="display: none;"
        x-show="showToast"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
    >
        <span class="text-lg leading-none mt-0.5">🚀</span>
        <div class="flex flex-col gap-1 text-left">
            <p class="font-bold text-white text-xs">¡Estamos creciendo!</p>
            <p class="text-[11px] font-normal leading-relaxed text-gray-300">
                Pocos restaurantes en esta zona para filtrar. ¡Recomiéndanos para crecer más rápido!
            </p>
        </div>
    </div>

    {{-- Estado de ubicación denegada --}}
    @if ($locationDenied)
        <div class="flex flex-col items-center justify-center gap-8 px-6 py-24 text-center">
            <span class="text-6xl text-red-500">
                <i class="bxf bx-lock-alt"></i>
            </span>
            <h2 class="text-2xl font-bold tracking-tight text-gray-800">
                Necesitamos acceso a tu ubicación
            </h2>
            <p class="max-w-xs text-sm leading-relaxed text-gray-500">
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
                    Prometemos no seguirte hasta tu cocina (solo hasta la puerta)
                </p>
            </div>
        </div>

        {{-- Hay ciudad y cobertura --}}
    @elseif($city)
        <div class="flex w-full flex-col gap-2">
            <div class="flex items-center justify-between px-4">
                <h2 class="font-bold text-gray-800">Explorar por categorías</h2>
            </div>

            <div class="no-scrollbar grid grid-flow-col auto-cols-[72px] justify-items-center gap-4 overflow-x-auto px-4">
                @foreach ($this->tags as $tag)
                    @php
                        $categories = [
                            'Tacos' => ['icon' => '🌮', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200/50'],
                            'Hamburguesas' => ['icon' => '🍔', 'bg' => 'bg-orange-50 text-orange-700 border-orange-200/50'],
                            'Pizza' => ['icon' => '🍕', 'bg' => 'bg-red-50 text-red-700 border-red-200/50'],
                            'Sushi' => ['icon' => '🍣', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200/50'],
                            'Mariscos' => ['icon' => '🍤', 'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200/50'],
                            'Vegano' => ['icon' => '🥗', 'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50'],
                            'Postres' => ['icon' => '🍰', 'bg' => 'bg-pink-50 text-pink-700 border-pink-200/50'],
                            'Café' => ['icon' => '☕', 'bg' => 'bg-amber-100 text-amber-900 border-amber-200'],
                            'Alitas' => ['icon' => '🍗', 'bg' => 'bg-yellow-50 text-yellow-800 border-yellow-200/50'],
                            'Desayunos' => ['icon' => '🍳', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200/50'],
                        ];
                        $cat = $categories[$tag->name] ?? ['icon' => '🍴', 'bg' => 'bg-gray-50 text-gray-700 border-gray-200/50'];
                    @endphp
                    <button
                        class="flex shrink-0 cursor-pointer flex-col items-center gap-2 transition-all active:scale-95"
                        type="button"
                        @click="triggerToast()"
                    >
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border text-3xl shadow-sm {{ $cat['bg'] }}">
                            {{ $cat['icon'] }}
                        </div>
                        <span class="text-xs font-semibold text-gray-700">{{ $tag->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex w-full flex-col gap-2 px-4">
            <h2 class="font-bold text-gray-800">
                Restaurantes cerca
            </h2>

            <div class="flex flex-col gap-4">
                @forelse($this->filteredBusinesses as $restaurant)
                    <x-ui.restaurant-card
                        :key="'res-' . $restaurant->id"
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
                    <div class="flex flex-col items-center py-10 text-center">
                        <i class="bxf bx-search-alt text-4xl text-gray-200"></i>
                        <p class="mt-2 text-balance text-sm text-gray-400">
                            No encontramos restaurantes en tu zona actualmente.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- No hay cobertura --}}
    @elseif($noService)
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
