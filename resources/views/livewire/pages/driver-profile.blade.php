<div class="flex flex-col gap-6 py-4">
    {{-- ENCABEZADO Y PERFIL BÁSICO --}}
    <x-ui.page-section>
        <x-ui.card>
            <div class="flex items-center gap-4">
                {{-- AVATAR O INICIALES --}}
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-red-500 text-xl font-bold text-white shadow-sm">
                    {{ $user?->initials() ?? 'D' }}
                </div>

                {{-- DETALLES DEL REPARTIDOR --}}
                <div class="flex-1 overflow-hidden">
                    <h2 class="truncate text-lg font-bold text-gray-900">
                        {{ $user?->name ?? ('Repartidor #' . $driver->id) }}
                    </h2>
                    <p class="truncate text-xs font-semibold text-gray-500">
                        {{ $user?->email ?? 'Sin correo' }}
                    </p>
                    @if($user?->phone)
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ $user->phone }}
                        </p>
                    @endif
                </div>

                {{-- BADGE DE ESTADO ACTUAL (Con colores directos de Tailwind para garantizar el cambio) --}}
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $isAvailable ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        @if($isAvailable)
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        @else
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                        {{ $driver->availability_status?->getLabel() ?? 'Sin estado' }}
                    </span>
                </div>
            </div>

            {{-- INFORMACIÓN ADICIONAL --}}
            <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 text-xs">
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block font-semibold uppercase text-[10px] text-gray-400">Ciudad</span>
                    <span class="font-bold text-gray-800">{{ $driver->city?->name ?? 'No asignada' }}</span>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block font-semibold uppercase text-[10px] text-gray-400">ID Repartidor</span>
                    <span class="font-bold text-gray-800">#{{ $driver->id }}</span>
                </div>
            </div>
        </x-ui.card>
    </x-ui.page-section>

    {{-- SECCIÓN DEL SLIDER DE ESTADO --}}
    <x-ui.page-section>
        @if(!$isAvailable)
            {{-- OPCIÓN 1: SI ESTÁ OFFLINE (Desconectado) -> SLIDER VERDE PARA CONECTARSE --}}
            <x-ui.card class="bg-emerald-50/50">
                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                    Desliza para cambiar tu estado
                </p>

                <x-ui.slider
                    wire:key="slider-offline-{{ $driver->availability_status?->value ?? 'offline' }}-{{ $driver->updated_at->timestamp }}"
                    label="Empezar a repartir >>"
                    color="bg-emerald-500"
                    icon="bxf bx-chevron-right"
                    iconColor="text-emerald-500"
                    action="$wire.toggleAvailability()"
                />
            </x-ui.card>
        @else
            {{-- OPCIÓN 2: SI ESTÁ ONLINE (Conectado) -> SLIDER ROJO PARA DESCONECTARSE --}}
            <x-ui.card class="bg-red-50/50">
                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">
                    Desliza para finalizar tu turno
                </p>

                <x-ui.slider
                    wire:key="slider-online-{{ $driver->availability_status?->value ?? 'online' }}-{{ $driver->updated_at->timestamp }}"
                    label="Dejar de repartir >>"
                    color="bg-red-500"
                    icon="bxf bx-chevron-right"
                    iconColor="text-red-500"
                    action="$wire.toggleAvailability()"
                />
            </x-ui.card>
        @endif
    </x-ui.page-section>
</div>