<div class="flex flex-col gap-6 py-4">
    {{-- ENCABEZADO Y PERFIL DEL RESTAURANTE --}}
    <x-ui.page-section>
        <x-ui.card>
            <div class="flex items-center gap-4">
                {{-- LOGO O IMAGEN DE REFERENCIA --}}
                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-red-500 text-xl font-bold text-white shadow-sm">
                    @if($business->logo_path)
                        <img src="{{ Storage::url($business->logo_path) }}" alt="{{ $business->name }}" class="h-full w-full object-cover" />
                    @else
                        {{ strtoupper(substr($business->name, 0, 2)) }}
                    @endif
                </div>

                {{-- DETALLES DEL RESTAURANTE --}}
                <div class="flex-1 overflow-hidden">
                    <h2 class="truncate text-lg font-bold text-gray-900">
                        {{ $business->name }}
                    </h2>
                    <p class="truncate text-xs font-semibold text-gray-500">
                        {{ $business->category?->name ?? 'Sin categoría' }} • {{ $business->email ?? 'Sin correo' }}
                    </p>
                    @if($business->phone)
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ $business->phone }}
                        </p>
                    @endif
                </div>

                {{-- BADGE DE ESTADO ACTUAL --}}
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold shadow-sm {{ $business->is_open ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        @if($business->is_open)
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Abierto
                        @else
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Cerrado
                        @endif
                    </span>
                </div>
            </div>

            {{-- INFORMACIÓN ADICIONAL --}}
            <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 text-xs">
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block text-[10px] font-semibold uppercase text-gray-400">Ciudad</span>
                    <span class="font-bold text-gray-800">{{ $business->city?->name ?? 'No asignada' }}</span>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block text-[10px] font-semibold uppercase text-gray-400">Dirección</span>
                    <span class="truncate font-bold text-gray-800 block" title="{{ $business->address }}">{{ $business->address ?? 'Sin dirección' }}</span>
                </div>
            </div>
        </x-ui.card>
    </x-ui.page-section>

    {{-- PESTAÑAS (TABS) DE NAVEGACIÓN INTERNA --}}
    <x-ui.page-section>
        <div class="flex border-b border-gray-200">
            <button 
                wire:click="setTab('staff')" 
                class="flex-1 py-3 text-center text-xs font-bold transition-colors border-b-2 {{ $activeTab === 'staff' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
            >
                Staff ({{ $business->users->count() }}) 
            </button>
            <button 
                wire:click="setTab('products')" 
                class="flex-1 py-3 text-center text-xs font-bold transition-colors border-b-2 {{ $activeTab === 'products' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
            >
                Gestión de Productos
            </button>
        </div>
    </x-ui.page-section>

    {{-- CONTENIDO DE PESTAÑAS --}}
    <x-ui.page-section>
        @if($activeTab === 'staff')
            {{-- TAB 1: ENCARGADOS + SLIDER DE ESTADO --}}
            <div class="flex flex-col gap-6">
                
                {{-- SECCIÓN DEL SLIDER DE ESTADO --}}
                <div>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Estado de Operación</h3>
                    @if(!$business->is_open)
                        {{-- OPCIÓN 1: SI ESTÁ CERRADO -> SLIDER VERDE PARA EMPEZAR A VENDER --}}
                        <x-ui.card class="bg-emerald-50/50">
                            <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                                Desliza para empezar a vender
                            </p>

                            <x-ui.slider
                                wire:key="slider-closed-{{ $business->is_open ? 'open' : 'closed' }}-{{ $business->updated_at->timestamp }}"
                                label="Empezar a vender >>"
                                color="bg-emerald-500"
                                icon="bxf bx-chevron-right"
                                iconColor="text-emerald-500"
                                action="$wire.toggleOpenStatus()"
                            />
                        </x-ui.card>
                    @else
                        {{-- OPCIÓN 2: SI ESTÁ ABIERTO -> SLIDER ROJO PARA DEJAR DE VENDER --}}
                        <x-ui.card class="bg-red-50/50">
                            <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">
                                Desliza para pausar las ventas
                            </p>

                            <x-ui.slider
                                wire:key="slider-open-{{ $business->is_open ? 'open' : 'closed' }}-{{ $business->updated_at->timestamp }}"
                                label="Dejar de vender >>"
                                color="bg-red-500"
                                icon="bxf bx-chevron-right"
                                iconColor="text-red-500"
                                action="$wire.toggleOpenStatus()"
                            />
                        </x-ui.card>
                    @endif
                </div>

                {{-- LISTA DE ENCARGADOS --}}
                <div class="flex flex-col gap-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Usuarios con acceso</h3>

                    @forelse($business->users as $staff)
                        <x-ui.card wire:key="staff-card-{{ $staff->id }}" class="flex items-center gap-3 p-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-200 text-sm font-bold text-gray-600">
                                {{ method_exists($staff, 'initials') ? $staff->initials() : strtoupper(substr($staff->name, 0, 2)) }}
                            </div>

                            <div class="flex-1 overflow-hidden">
                                <h4 class="truncate text-sm font-bold text-gray-800">
                                    {{ $staff->name }}
                                </h4>
                                <p class="truncate text-xs text-gray-400">
                                    {{ $staff->email }}
                                </p>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.card class="py-8 text-center text-xs text-gray-400">
                            No hay encargados registrados para este restaurante.
                        </x-ui.card>
                    @endforelse
                </div>

            </div>

        @elseif($activeTab === 'products')
            {{-- TAB 2: PRODUCTOS / AGOTADOS --}}
            <div class="flex flex-col gap-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Disponibilidad de Productos</h3>

                @forelse($business->products as $product)
                    <x-ui.card wire:key="product-card-{{ $product->id }}" class="flex items-center justify-between gap-3 p-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            {{-- IMAGEN DEL PRODUCTO --}}
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 text-gray-400">
                                @if($product->image_path)
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                                @else
                                    <span class="text-xs font-bold">{{ substr($product->name, 0, 1) }}</span>
                                @endif
                            </div>

                            <div class="overflow-hidden">
                                <h4 class="truncate text-sm font-bold text-gray-800 {{ !$product->is_available ? 'line-through text-gray-400' : '' }}">
                                    {{ $product->name }}
                                </h4>
                                <p class="text-xs font-semibold text-gray-500">
                                    ${{ number_format($product->price, 2) }}
                                </p>
                            </div>
                        </div>

                        {{-- BOTÓN DE ALTERNAR DISPONIBILIDAD (Refleja el estado actual) --}}
                        <button 
                            wire:click="toggleProductAvailability({{ $product->id }})"
                            class="shrink-0 rounded-xl px-3 py-1.5 text-xs font-bold shadow-sm transition-all active:scale-95 {{ $product->is_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
                        >
                            {{ $product->is_available ? 'Disponible' : 'Agotado' }}
                        </button>
                    </x-ui.card>
                @empty
                    <x-ui.card class="py-8 text-center text-xs text-gray-400">
                        No hay productos registrados en este negocio.
                    </x-ui.card>
                @endforelse
            </div>
        @endif
    </x-ui.page-section>
</div>