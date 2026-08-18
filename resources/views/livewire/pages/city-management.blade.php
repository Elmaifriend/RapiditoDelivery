<div>
    <div class="flex flex-col gap-6 py-4">
        {{-- Header con Selector de Ciudad --}}
        <x-ui.page-section>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Gestión Territorial
                        @if ($this->selectedCity)
                            <span class="font-bold text-red-500">#{{ $this->selectedCity->name }}</span>
                        @endif
                    </p>
                    <h1 class="text-xl font-bold text-gray-900">Repartidores y Restaurantes</h1>
                </div>

                {{-- Buscador / Selector de Ciudad --}}
                <div class="relative w-full md:w-72" x-data="{ open: false }">
                    <div class="relative">
                        <i class="bxf bx-search absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-400"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            @focus="open = true"
                            @click.outside="open = false"
                            placeholder="Buscar ciudad..."
                            class="w-full rounded-2xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm font-semibold text-gray-900 shadow-2xs placeholder:text-gray-400 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                        />
                    </div>

                    {{-- Menú desplegable de sugerencias --}}
                    <div
                        x-show="open"
                        x-transition
                        class="absolute z-50 mt-2 max-h-60 w-full overflow-y-auto rounded-2xl border border-gray-100 bg-white p-2 shadow-xl"
                        style="display: none;"
                    >
                        @forelse ($this->cities as $city)
                            <button
                                type="button"
                                wire:click="selectCity({{ $city->id }})"
                                @click="open = false"
                                class="{{ $selectedCityId === $city->id ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-700 hover:bg-gray-50' }} flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm transition-all"
                            >
                                <span>{{ $city->name }}</span>
                                <span class="text-xs text-gray-400">{{ $city->state }}</span>
                            </button>
                        @empty
                            <div class="px-3 py-2 text-xs font-semibold text-gray-400">
                                No se encontraron ciudades.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-ui.page-section>

        @if (session()->has('message'))
            <x-ui.page-section>
                <x-ui.card class="border-emerald-200! bg-emerald-50! text-emerald-800!">
                    <div class="flex items-center gap-2">
                        <i class="bxf bx-check-circle text-xl text-emerald-500"></i>
                        <span>{{ session('message') }}</span>
                    </div>
                </x-ui.card>
            </x-ui.page-section>
        @endif

        @if ($selectedCityId)
            {{-- Tabs de Navegación --}}
            <x-ui.page-section>
                <div class="flex gap-1 rounded-2xl border border-gray-200 bg-gray-100 p-1">
                    <button
                        type="button"
                        class="{{ $activeTab === 'repartidores' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }} flex-1 rounded-xl py-2.5 text-sm font-bold transition-all"
                        wire:click="changeTab('repartidores')"
                    >
                        Repartidores
                        <span
                            class="{{ $activeTab === 'repartidores' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500' }} ml-1 rounded-lg px-2 py-0.5 text-xs font-bold"
                        >
                            {{ $this->drivers->count() }}
                        </span>
                    </button>

                    <button
                        type="button"
                        class="{{ $activeTab === 'restaurantes' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }} flex-1 rounded-xl py-2.5 text-sm font-bold transition-all"
                        wire:click="changeTab('restaurantes')"
                    >
                        Restaurantes
                        <span
                            class="{{ $activeTab === 'restaurantes' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500' }} ml-1 rounded-lg px-2 py-0.5 text-xs font-bold"
                        >
                            {{ $this->businesses->count() }}
                        </span>
                    </button>
                </div>
            </x-ui.page-section>

            {{-- Contenido de las Tabs --}}
            <x-ui.page-section>
                <div class="pb-10">
                    @if ($activeTab === 'repartidores')
                        {{-- TAB: REPARTIDORES --}}
                        @if ($this->drivers->isEmpty())
                            <div class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-4xl text-red-500 shadow-sm">
                                    <i class="bxf bx-cycling"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Sin repartidores</h3>
                                <p class="mt-2 max-w-[250px] text-sm text-gray-500">
                                    No hay repartidores registrados en {{ $this->selectedCity?->name }}.
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($this->drivers as $driver)
                                    <x-ui.card wire:key="driver-{{ $driver->id }}">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                            {{-- Info Repartidor --}}
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-xl font-bold text-gray-700">
                                                    <i class="bxf bx-user"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-bold text-gray-900">
                                                        {{ $driver->user->name ?? 'Repartidor #' . $driver->id }}
                                                    </h3>
                                                    <p class="text-xs font-semibold text-gray-500">
                                                        {{ $driver->user->email ?? 'Sin email' }}
                                                    </p>
                                                    
                                                    <div class="mt-1 flex items-center gap-1.5">
                                                        <x-ui.badge :color="$driver->is_active ? 'green' : 'gray'">
                                                            {{ $driver->is_active ? 'Plataforma: Activo' : 'Plataforma: Inactivo' }}
                                                        </x-ui.badge>

                                                        @php
                                                            $statusValue = is_object($driver->availability_status) && property_exists($driver->availability_status, 'value')
                                                                ? $driver->availability_status->value
                                                                : (string) $driver->availability_status;

                                                            $statusLabel = is_object($driver->availability_status) && method_exists($driver->availability_status, 'label')
                                                                ? $driver->availability_status->label()
                                                                : ucfirst($statusValue);
                                                        @endphp

                                                        <x-ui.badge :color="$statusValue === 'online' ? 'blue' : 'amber'">
                                                            {{ $statusLabel }}
                                                        </x-ui.badge>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Botones de Acción Repartidor --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                {{-- Botón Enviar Link Panel --}}
                                                <button
                                                    type="button"
                                                    wire:click="sendDriverPanelLink({{ $driver->id }})"
                                                    title="Enviar enlace de panel"
                                                    class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-2xs transition-all"
                                                >
                                                    <i class="bxf bx-send text-sm text-blue-500"></i>
                                                    <span>Enviar Link</span>
                                                </button>

                                                {{-- Botón Disponibilidad (Online / Offline) --}}
                                                <button
                                                    type="button"
                                                    wire:click="toggleDriverAvailability({{ $driver->id }})"
                                                    class="inline-flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-bold shadow-2xs transition-all {{ $statusValue === 'online' ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100' }}"
                                                >
                                                    <i class="bxf {{ $statusValue === 'online' ? 'bx-time' : 'bx-check-circle' }} text-sm"></i>
                                                    <span>{{ $statusValue === 'online' ? 'Poner Ocupado/Offline' : 'Poner Disponible' }}</span>
                                                </button>

                                                {{-- Botón Activar / Desactivar en plataforma --}}
                                                <button
                                                    type="button"
                                                    wire:click="toggleDriverActive({{ $driver->id }})"
                                                    class="inline-flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-bold shadow-2xs transition-all {{ $driver->is_active ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                                >
                                                    <i class="bxf {{ $driver->is_active ? 'bx-block' : 'bx-power-off' }} text-sm"></i>
                                                    <span>{{ $driver->is_active ? 'Desactivar' : 'Activar' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </x-ui.card>
                                @endforeach
                            </div>
                        @endif
                    @else
                        {{-- TAB: RESTAURANTES --}}
                        @if ($this->businesses->isEmpty())
                            <div class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-4xl text-blue-500 shadow-sm">
                                    <i class="bxf bx-store"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Sin restaurantes</h3>
                                <p class="mt-2 max-w-[250px] text-sm text-gray-500">
                                    No hay restaurantes o negocios registrados en {{ $this->selectedCity?->name }}.
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($this->businesses as $business)
                                    <x-ui.card wire:key="business-{{ $business->id }}">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                            {{-- Info Restaurante --}}
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-xl font-bold text-gray-700">
                                                    <i class="bxf bx-restaurant"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="line-clamp-2 break-words text-base font-bold text-gray-900">
                                                        {{ $business->name }}
                                                    </h3>
                                                    <p class="text-xs font-semibold text-gray-500">
                                                        {{ $business->address ?? 'Sin dirección' }}
                                                    </p>

                                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                        <x-ui.badge :color="$business->is_open ? 'green' : 'gray'">
                                                            {{ $business->is_open ? 'Abierto' : 'Cerrado' }}
                                                        </x-ui.badge>

                                                        <x-ui.badge :color="$business->accepts_delivery ? 'blue' : 'amber'">
                                                            {{ $business->accepts_delivery ? 'Envíos Habilitados' : 'Envíos Deshabilitados' }}
                                                        </x-ui.badge>

                                                        <x-ui.badge :color="$business->status === 'active' ? 'emerald' : 'gray'">
                                                            {{ $business->status === 'active' ? 'Visible' : 'Oculto' }}
                                                        </x-ui.badge>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Botones de Acción Restaurante --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                {{-- Botón Enviar Link Panel --}}
                                                <button
                                                    type="button"
                                                    wire:click="sendBusinessPanelLink({{ $business->id }})"
                                                    title="Enviar enlace del panel del restaurante"
                                                    class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-2xs transition-all"
                                                >
                                                    <i class="bxf bx-send text-sm text-blue-500"></i>
                                                    <span>Enviar Link</span>
                                                </button>

                                                {{-- Botón Abrir / Cerrar --}}
                                                <button
                                                    type="button"
                                                    wire:click="toggleBusinessOpen({{ $business->id }})"
                                                    class="inline-flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-bold shadow-2xs transition-all {{ $business->is_open ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                                >
                                                    <i class="bxf {{ $business->is_open ? 'bx-door-open' : 'bx-lock-alt' }} text-sm"></i>
                                                    <span>{{ $business->is_open ? 'Cerrar' : 'Abrir' }}</span>
                                                </button>

                                                {{-- Botón Habilitar / Deshabilitar Envíos --}}
                                                <button
                                                    type="button"
                                                    wire:click="toggleBusinessDelivery({{ $business->id }})"
                                                    class="inline-flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-bold shadow-2xs transition-all {{ $business->accepts_delivery ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100' }}"
                                                >
                                                    <i class="bxf bx-cycling text-sm"></i>
                                                    <span>{{ $business->accepts_delivery ? 'Deshabilitar Envíos' : 'Habilitar Envíos' }}</span>
                                                </button>

                                                {{-- Botón Mostrar / Ocultar (status) --}}
                                                <button
                                                    type="button"
                                                    wire:click="toggleBusinessStatus({{ $business->id }})"
                                                    class="inline-flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-bold shadow-2xs transition-all {{ $business->status === 'active' ? 'border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-200' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                                >
                                                    <i class="bxf {{ $business->status === 'active' ? 'bx-hide' : 'bx-show' }} text-sm"></i>
                                                    <span>{{ $business->status === 'active' ? 'Ocultar' : 'Mostrar' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </x-ui.card>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </x-ui.page-section>
        @else
            <x-ui.page-section>
                <div class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center">
                    <h3 class="text-lg font-bold text-gray-900">Selecciona una ciudad</h3>
                    <p class="mt-2 max-w-[250px] text-sm text-gray-500">
                        Usa el buscador superior para elegir la ciudad a gestionar.
                    </p>
                </div>
            </x-ui.page-section>
        @endif
    </div>
</div>