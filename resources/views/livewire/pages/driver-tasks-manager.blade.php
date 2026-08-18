@extends('layouts.page')

@assets
<link rel="preconnect" href="https://unpkg.com" crossorigin>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
@endassets

@section('content')
    <header class="flex items-center justify-between px-4 py-3 bg-white shadow-sm">
        <div class="flex items-center gap-2">
            <h1 class="text-base font-bold text-gray-800">Mi App</h1>
        </div>

        {{-- ACCESO AL PERFIL DEL REPARTIDOR --}}
        <a 
            href="{{ route('driver.profile', ['driver' => $driver->id]) }}" 
            wire:navigate
            class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
            title="Perfil de Repartidor"
        >
            <i class="bx bx-user-circle text-xl"></i>
        </a>
    </header>
    <div
        class="flex flex-col gap-6 py-4"
        wire:poll.5s="loadActiveOrder"
    >
        <x-ui.page-section>
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500">
                    Panel Repartidor
                    <span class="font-bold text-red-500">{{ $driver->name ?? 'ID #' . $driver->id }}</span>
                </p>
                <x-ui.badge
                    color="green"
                    :pulse="true"
                >
                    Activo
                </x-ui.badge>
            </div>
        </x-ui.page-section>

        {{-- SI NO HAY PEDIDO ASIGNADO --}}
        @if (!$currentOrder)
            <x-ui.page-section>
                <div
                    class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center"
                    wire:key="no-order-assigned"
                >
                    <div
                        class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-4xl text-red-500 shadow-sm">
                        <i class="bxf bx-package"></i>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900">No hay pedidos en este momento</h3>
                    <p class="mt-2 max-w-[250px] text-sm text-gray-500">
                        Estás en línea y disponible. Tan pronto como un restaurante marque una orden lista, se te asignará
                        automáticamente.
                    </p>

                    <div class="mt-6">
                        <x-ui.badge
                            color="amber"
                            :pulse="true"
                        >
                            Esperando tareas...
                        </x-ui.badge>
                    </div>
                </div>
            </x-ui.page-section>
        @else
            {{-- SI HAY UN PEDIDO ASIGNADO --}}
            @php
                $restaurant = $currentOrder->business;

                // Estado de la entrega
                $isHeadingToRestaurant =
                    $currentOrder->delivery_status === \App\Enums\DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT;
            @endphp

            <x-ui.page-section>
                <div
                    class="mb-4 mt-2 flex flex-col items-center space-y-3 text-center"
                    wire:key="header-order-{{ $currentOrder->id }}-{{ $currentOrder->delivery_status->value }}"
                >
                    <x-ui.badge>
                        <span class="font-bold">Orden #{{ $currentOrder->id }}</span>
                    </x-ui.badge>

                    @if ($isHeadingToRestaurant)
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Ve a recoger este pedido</h1>
                        <p class="flex items-center justify-center gap-1.5 text-xs font-bold text-amber-600">
                            <span class="h-2 w-2 animate-ping rounded-full bg-amber-500"></span>
                            Paso 1: Dirígete al Restaurante
                        </p>
                    @else
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Entrega este pedido</h1>
                        <p class="flex items-center justify-center gap-1.5 text-xs font-bold text-blue-600">
                            <span class="h-2 w-2 animate-ping rounded-full bg-blue-500"></span>
                            Paso 2: En trayecto al cliente
                        </p>
                    @endif
                </div>
            </x-ui.page-section>

            <x-ui.page-section>
                {{-- CONTENEDOR PRINCIPAL CON ALPINE Y WIRE:KEY UNIFICADO --}}
                <div
                    class="pb-10"
                    wire:key="order-container-{{ $currentOrder->id }}-{{ $currentOrder->delivery_status->value }}"
                    x-data="{ step: '{{ $isHeadingToRestaurant ? 'pickup' : 'deliver' }}' }"
                    x-effect="step = '{{ $isHeadingToRestaurant ? 'pickup' : 'deliver' }}'"
                >

                    {{-- ======================================================== --}}
                    {{-- ETAPA 1: IR AL RESTAURANTE --}}
                    {{-- ======================================================== --}}
                    @if ($isHeadingToRestaurant)
                        {{-- VISTA DE DIRECCIÓN DEL RESTAURANTE + MAPA --}}
                        <div
                            class="space-y-6"
                            x-show="step === 'pickup'"
                        >
                            <x-ui.card>
                                <div class="mb-4 flex items-start justify-between border-b border-gray-100 pb-4">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 flex-none flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                            <i class="bxf bx-store-alt text-xl text-gray-400"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="line-clamp-2 break-words text-base font-bold text-gray-900">
                                                {{ $restaurant->name ?? 'Restaurante' }}</h3>
                                            <p class="mt-0.5 line-clamp-2 break-words text-xs font-semibold text-gray-500">
                                                {{ $restaurant->address ?? 'Sin dirección' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- MAPA DEL RESTAURANTE --}}
                                <div class="mb-4 overflow-hidden rounded-2xl border border-gray-100">
                                    <x-ui.map
                                        :lat="$restaurant->lat ?? 32.5149"
                                        :lng="$restaurant->lng ?? -117.0382"
                                    />
                                </div>

                                {{-- LINK A GOOGLE MAPS GPS --}}
                                <a
                                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border border-red-100 bg-red-50 py-3 text-xs font-bold text-red-500 transition-colors hover:bg-red-100 active:scale-[0.98]"
                                    href="{{ $restaurantMapsUrl }}"
                                    target="_blank"
                                >
                                    <i class="bxf bx-navigation text-base"></i>
                                    Abrir en Google Maps GPS
                                </a>
                            </x-ui.card>

                            {{-- SLIDER PARA LLEGAR AL RESTAURANTE --}}
                            <x-ui.card class="bg-gray-50/50">
                                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                    Desliza
                                    cuando llegues al restaurante</p>

                                <x-ui.slider
                                    label="Llegué al Restaurante >>"
                                    color="bg-red-500"
                                    icon="bxf bx-chevron-right"
                                    iconColor="text-red-500"
                                    action="setTimeout(() => { step = 'checklist'; }, 200)"
                                    x-on:reset-pickup-slider.window="currentX = 0; completed = false; isDragging = false;"
                                />
                            </x-ui.card>
                        </div>

                        {{-- CHECKLIST Y CONFIRMACIÓN DE PRODUCTOS EN RESTAURANTE --}}
                        <div
                            class="space-y-6"
                            x-show="step === 'checklist'"
                            x-cloak
                            x-data="{ checkedItems: {} }"
                        >
                            <x-ui.card>
                                <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                            <i class="bxf bx-checklist text-xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">
                                            Verificación de Productos
                                        </h3>
                                    </div>
                                    <button
                                        class="cursor-pointer rounded-xl bg-red-50 px-3 py-1.5 text-xs font-bold text-red-500 hover:bg-red-100 active:scale-95"
                                        x-on:click="step = 'pickup'; $dispatch('reset-pickup-slider')"
                                    >
                                        Volver
                                    </button>
                                </div>

                                <div class="mb-4 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                    <div class="divide-y divide-gray-100">
                                        @foreach ($currentOrder->items as $index => $item)
                                            <div
                                                class="-mx-2 flex cursor-pointer select-none items-center justify-between rounded-xl px-2 py-2.5 transition-all active:bg-gray-100"
                                                x-on:click="checkedItems[{{ $index }}] = !checkedItems[{{ $index }}]"
                                            >
                                                <span class="flex items-center gap-2 text-xs font-semibold text-gray-800">
                                                    <i
                                                        class="bxf text-lg transition-all"
                                                        :class="checkedItems[{{ $index }}] ?
                                                            'bx-check-square text-emerald-500' :
                                                            'bx-checkbox text-gray-400'"
                                                    ></i>
                                                    <span>
                                                        <strong
                                                            class="mr-1.5 rounded bg-gray-200 px-1.5 py-0.5 font-bold text-gray-900"
                                                        >{{ $item->quantity }}x</strong>
                                                        {{ $item->product_name_snapshot }}
                                                    </span>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- REPORTES DEL CONDUCTOR --}}
                                <div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-4">
                                    <span class="text-sm font-bold text-gray-500">¿Todo está en orden?</span>
                                </div>

                                <div class="flex gap-3">
                                    <button
                                        class="flex flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl py-2.5 text-xs font-bold transition-all active:scale-[0.98]"
                                        type="button"
                                        x-on:click="$wire.set('allItemsCorrect', true)"
                                        x-bind:class="$wire.allItemsCorrect ? 'bg-emerald-500 text-white shadow-sm' :
                                            'border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                    >
                                        <i class="bxf bx-check text-xl"></i> Correcto
                                    </button>
                                    <button
                                        class="flex flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl py-2.5 text-xs font-bold transition-all active:scale-[0.98]"
                                        type="button"
                                        x-on:click="$wire.set('allItemsCorrect', false)"
                                        x-bind:class="!$wire.allItemsCorrect ? 'bg-red-500 text-white shadow-sm' :
                                            'border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                    >
                                        <i class="bxf bx-x text-xl"></i> Detalle
                                    </button>
                                </div>

                                <div class="mt-4 space-y-1.5">
                                    <textarea
                                        class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs font-semibold text-gray-800 placeholder-gray-400 transition-all focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                                        wire:model="driverNotes"
                                        rows="4"
                                        placeholder="Escribe aquí si hubo algún detalle..."
                                    ></textarea>
                                </div>
                            </x-ui.card>

                            {{-- SLIDER CONFIRMACIÓN DE RECOGIDA CON ACCIÓN DE LIVEWIRE --}}
                            <x-ui.card class="bg-emerald-50/50">
                                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                    Desliza
                                    para Iniciar Viaje al Cliente</p>

                                <x-ui.slider
                                    label="Confirmar Recolección >>"
                                    color="bg-emerald-500"
                                    icon="bxf bx-chevron-right"
                                    iconColor="text-emerald-500"
                                    action="$wire.markAsPickedUp()"
                                />
                            </x-ui.card>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- ETAPA 2: IR AL CLIENTE (EN DOS PASOS) --}}
                        {{-- ======================================================== --}}
                    @else
                        {{-- PASO 2A: VISTA DE DIRECCIÓN Y NAVEGACIÓN HASTA LLEGAR AL DOMICILIO --}}
                        <div
                            class="space-y-6"
                            x-show="step === 'deliver'"
                        >
                            {{-- TARJETA DEL CLIENTE --}}
                            <x-ui.card>
                                <div class="mb-4 flex items-start justify-between gap-4 border-b border-gray-100 pb-4">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 flex-none flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                            <i class="bxf bx-user text-xl text-gray-400"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="line-clamp-2 break-words text-base font-bold text-gray-900">
                                                {{ $currentOrder->customer_name ?? 'Cliente' }}</h3>
                                            <p class="mt-0.5 line-clamp-2 break-words text-xs font-semibold text-gray-500">
                                                {{ $dropoff?->formatted_address ?? ($dropoff?->address_line ?? 'Sin dirección disponible') }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($currentOrder->customer_phone)
                                        <a
                                            class="flex shrink-0 items-center gap-1 rounded-xl bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-500 transition-colors hover:bg-red-100 active:scale-95"
                                            href="tel:{{ $currentOrder->customer_phone }}"
                                        >
                                            <i class="bxf bx-phone text-sm"></i>
                                            Llamar
                                        </a>
                                    @endif
                                </div>

                                @if ($dropoff?->reference)
                                    <div
                                        class="mb-4 flex items-start gap-2 rounded-xl border border-amber-100/50 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                                        <i class="bxf bx-info-circle mt-0.5 text-base text-amber-500"></i>
                                        <div>
                                            <span class="mb-0.5 block font-bold">Referencias de entrega:</span>
                                            <p class="leading-relaxed">{{ $dropoff->reference }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($dropoff?->delivery_instructions)
                                    <div
                                        class="mb-4 flex items-start gap-2 rounded-xl border border-blue-100/50 bg-blue-50 p-3 text-xs font-medium text-blue-800">
                                        <i class="bxf bx-message-detail mt-0.5 text-base text-blue-500"></i>
                                        <div>
                                            <span class="mb-0.5 block font-bold">Indicaciones para el repartidor:</span>
                                            <p class="leading-relaxed">{{ $dropoff->delivery_instructions }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- MAPA DEL CLIENTE --}}
                                <div class="mb-4 overflow-hidden rounded-2xl border border-gray-100">
                                    <x-ui.map
                                        :lat="$dropoff->lat ?? 32.5149"
                                        :lng="$dropoff->lng ?? -117.0382"
                                    />
                                </div>

                                {{-- LINK A GOOGLE MAPS GPS CLIENTE --}}
                                <a
                                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border border-red-100 bg-red-50 py-3 text-xs font-bold text-red-500 transition-colors hover:bg-red-100 active:scale-[0.98]"
                                    href="{{ $dropoffMapsUrl }}"
                                    target="_blank"
                                >
                                    <i class="bxf bx-navigation text-base"></i>
                                    Abrir en Google Maps GPS
                                </a>
                            </x-ui.card>

                            {{-- RESUMEN DE LA ORDEN --}}
                            <x-ui.card>
                                <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                            <i class="bxf bx-receipt text-xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">
                                            Resumen del Pedido
                                        </h3>
                                    </div>
                                    <x-ui.badge :color="$currentOrder->payment_method->value === 'card' ? 'green' : 'amber'">
                                        {{ $currentOrder->payment_method->value === 'card' ? 'Tarjeta' : 'Efectivo' }}
                                    </x-ui.badge>
                                </div>

                                <div class="mb-3">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tienda de
                                        origen
                                    </p>
                                    <p class="mt-0.5 line-clamp-2 break-words text-sm font-bold text-gray-900">
                                        {{ $restaurant->name ?? 'Restaurante' }}
                                    </p>
                                </div>

                                <div class="mb-4 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                    <div class="divide-y divide-gray-100">
                                        @foreach ($currentOrder->items as $item)
                                            <x-ui.product-item
                                                :quantity="$item->quantity"
                                                :name="$item->product_name_snapshot"
                                                :price="$item->subtotal"
                                            />
                                        @endforeach
                                    </div>
                                </div>

                                @if ($currentOrder->special_instructions)
                                    <div
                                        class="mb-4 flex items-start gap-2 rounded-xl border border-amber-100/50 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                                        <i class="bxf bx-info-circle mt-0.5 text-base text-amber-500"></i>
                                        <p class="leading-relaxed">{{ $currentOrder->special_instructions }}</p>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                    <span class="text-sm font-bold text-gray-500">Total a cobrar</span>
                                    <span
                                        class="text-xl font-bold text-red-500">${{ number_format($currentOrder->total, 2) }}</span>
                                </div>
                            </x-ui.card>

                            {{-- SLIDER: LLEGUÉ AL DOMICILIO --}}
                            <x-ui.card class="bg-gray-50/50">
                                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                    Desliza cuando llegues con el cliente</p>

                                <x-ui.slider
                                    label="Llegué al Domicilio >>"
                                    color="bg-red-500"
                                    icon="bxf bx-chevron-right"
                                    iconColor="text-red-500"
                                    action="setTimeout(() => { step = 'arrived'; }, 200)"
                                    x-on:reset-arrived-slider.window="currentX = 0; completed = false; isDragging = false;"
                                />
                            </x-ui.card>
                        </div>

                        {{-- PASO 2B: REGISTRO DE ESTADO DE PAGO, INCIDENCIAS Y FINALIZACIÓN --}}
                        <div
                            class="space-y-6"
                            x-show="step === 'arrived'"
                            x-cloak
                        >
                            <x-ui.card>
                                <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                            <i class="bxf bx-wallet text-xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">
                                            Confirmación de Pago
                                        </h3>
                                    </div>
                                    <button
                                        class="cursor-pointer rounded-xl bg-red-50 px-3 py-1.5 text-xs font-bold text-red-500 hover:bg-red-100 active:scale-95"
                                        x-on:click="step = 'deliver'; $dispatch('reset-arrived-slider')"
                                    >
                                        Volver
                                    </button>
                                </div>

                                {{-- Opciones de Pago / Resultado --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Resultado
                                        de la
                                        entrega</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        <button
                                            class="{{ $paymentOutcome === 'paid_correctly' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 shadow-sm' : 'border-gray-200 bg-gray-50 text-gray-600' }} flex cursor-pointer items-center justify-between rounded-xl border p-3.5 text-xs font-bold transition-all active:scale-[0.98]"
                                            type="button"
                                            wire:click="$set('paymentOutcome', 'paid_correctly')"
                                        >
                                            <span class="flex items-center gap-2">
                                                <i
                                                    class="bxf bx-check-circle {{ $paymentOutcome === 'paid_correctly' ? 'text-emerald-500' : 'text-gray-400' }} text-base"></i>
                                                Pagado correctamente
                                            </span>
                                            @if ($paymentOutcome === 'paid_correctly')
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            @endif
                                        </button>

                                        <button
                                            class="{{ $paymentOutcome === 'client_refused_payment' ? 'border-amber-200 bg-amber-50 text-amber-800 shadow-sm' : 'border-gray-200 bg-gray-50 text-gray-600' }} flex cursor-pointer items-center justify-between rounded-xl border p-3.5 text-xs font-bold transition-all active:scale-[0.98]"
                                            type="button"
                                            wire:click="$set('paymentOutcome', 'client_refused_payment')"
                                        >
                                            <span class="flex items-center gap-2">
                                                <i
                                                    class="bxf bx-cart-x {{ $paymentOutcome === 'client_refused_payment' ? 'text-amber-500' : 'text-gray-400' }} text-base"></i>
                                                Cliente rechaza pagar
                                            </span>
                                            @if ($paymentOutcome === 'client_refused_payment')
                                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                            @endif
                                        </button>

                                        <button
                                            class="{{ $paymentOutcome === 'client_refused_delivery' ? 'border-red-200 bg-red-50 text-red-800 shadow-sm' : 'border-gray-200 bg-gray-50 text-gray-600' }} flex cursor-pointer items-center justify-between rounded-xl border p-3.5 text-xs font-bold transition-all active:scale-[0.98]"
                                            type="button"
                                            wire:click="$set('paymentOutcome', 'client_refused_delivery')"
                                        >
                                            <span class="flex items-center gap-2">
                                                <i
                                                    class="bxf bx-x-circle {{ $paymentOutcome === 'client_refused_delivery' ? 'text-red-500' : 'text-gray-400' }} text-base"></i>
                                                Cliente rechaza pedido / Devolución
                                            </span>
                                            @if ($paymentOutcome === 'client_refused_delivery')
                                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                {{-- Campo opcional para notas si hay alguna incidencia --}}
                                @if ($paymentOutcome !== 'paid_correctly')
                                    <div
                                        class="mt-4 space-y-1.5"
                                        x-transition
                                    >
                                        <label
                                            class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Detalle
                                            de
                                            la incidencia</label>
                                        <textarea
                                            class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs font-semibold text-gray-800 placeholder-gray-400 transition-all focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                                            wire:model="driverNotes"
                                            rows="4"
                                            placeholder="Describe por qué el cliente no pagó o rechazó el pedido..."
                                        ></textarea>
                                    </div>
                                @endif
                            </x-ui.card>

                            {{-- SLIDER FINALIZAR ENTREGA --}}
                            <x-ui.card class="bg-emerald-50/50">
                                <p class="mb-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                    Desliza para Finalizar Entrega</p>

                                <x-ui.slider
                                    label="Finalizar Entrega >>"
                                    color="bg-emerald-500"
                                    icon="bxf bx-chevron-right"
                                    iconColor="text-emerald-500"
                                    action="$wire.completeDelivery()"
                                />
                            </x-ui.card>
                        </div>
                    @endif
                </div>
            </x-ui.page-section>
        @endif
    </div>
@endsection
