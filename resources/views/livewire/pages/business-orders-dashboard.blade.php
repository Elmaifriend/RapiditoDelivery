@extends('layouts.page')

@php
    use App\Enums\DeliveryStatus;
@endphp

@section('content')
    <div
        class="flex flex-col gap-6 py-4"
        wire:poll.5s
    >
        <x-ui.page-section>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <p class="text-sm font-semibold text-gray-500">
                        Panel de Negocio
                        <span class="font-bold text-red-500">#{{ $businessId }}</span>
                    </p>

                    {{-- Botón / Ícono de Configuración del Perfil --}}
                    <a 
                        href="{{ route('businesses.profile', ['business' => $businessId]) }}" 
                        class="flex h-8 w-8 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-2xs transition-all hover:bg-gray-50 hover:text-gray-900 active:scale-95"
                        title="Configuración del restaurante"
                        wire:navigate
                    >
                        <i class="bxf bx-cog text-lg"></i>
                    </a>
                </div>

                <x-ui.badge
                    color="green"
                    :pulse="true"
                >
                    Sistema Activo
                </x-ui.badge>
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

        <x-ui.page-section>
            <div class="flex gap-1 rounded-2xl border border-gray-200 bg-gray-100">
                <button
                    class="{{ $activeTab === 'nuevos' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }} flex-1 rounded-xl py-2.5 text-sm font-bold transition-all"
                    wire:click="changeTab('nuevos')"
                >
                    Nuevos
                    <span
                        class="{{ $activeTab === 'nuevos' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500' }} ml-1 rounded-lg px-2 py-0.5 text-xs font-bold"
                    >
                        {{ $pedidosNuevos?->count() ?? 0 }}
                    </span>
                </button>

                <button
                    class="{{ $activeTab === 'preparacion' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }} flex-1 rounded-xl py-2.5 text-sm font-bold transition-all"
                    wire:click="changeTab('preparacion')"
                >
                    En Cocina
                    <span
                        class="{{ $activeTab === 'preparacion' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500' }} ml-1 rounded-lg px-2 py-0.5 text-xs font-bold"
                    >
                        {{ $pedidosAceptados?->count() ?? 0 }}
                    </span>
                </button>
            </div>
        </x-ui.page-section>

        <x-ui.page-section>
            <div class="pb-10">
                @if ($activeTab === 'nuevos')
                    {{-- PESTAÑA: NUEVOS PEDIDOS --}}
                    @if (blank($pedidosNuevos) || $pedidosNuevos->isEmpty())
                        <div
                            class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center">
                            <div
                                class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-4xl text-red-500 shadow-sm">
                                <i class="bxf bx-bell animate-swing"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Sin órdenes por aceptar</h3>
                            <p class="mt-2 max-w-[250px] text-sm text-gray-500">No hay pedidos pendientes en este momento.
                                Te avisaremos cuando llegue uno nuevo.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($pedidosNuevos as $pedido)
                                <x-ui.card wire:key="new-order-{{ $pedido->id }}">
                                    <div class="mb-4 flex items-start justify-between border-b border-gray-100 pb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                                <span class="-mb-1 text-[10px] uppercase text-gray-400">#</span>
                                                <span class="text-base leading-none">{{ $pedido->id }}</span>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900">
                                                    {{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                                <p
                                                    class="mt-0.5 flex items-center gap-1 text-xs font-semibold text-gray-500">
                                                    <i class="bxf bx-phone text-gray-400"></i>
                                                    {{ $pedido->customer_phone ?? 'Sin teléfono' }}
                                                </p>
                                            </div>
                                        </div>
                                        <x-ui.badge
                                            color="amber"
                                            :pulse="true"
                                        >
                                            Por Aceptar
                                        </x-ui.badge>
                                    </div>

                                    <div class="mb-4 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                        <div class="divide-y divide-gray-100">
                                            @foreach ($pedido->items ?? [] as $item)
                                                <x-ui.product-item
                                                    :quantity="$item->quantity"
                                                    :name="$item->product_name_snapshot"
                                                    :price="$item->subtotal"
                                                />
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($pedido->special_instructions)
                                        <div
                                            class="mb-4 flex items-start gap-2 rounded-xl border border-amber-100/50 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                                            <i class="bxf bx-info-circle mt-0.5 text-base text-amber-500"></i>
                                            <p class="leading-relaxed">{{ $pedido->special_instructions }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-2">
                                        <span class="text-sm font-bold text-gray-500">Total</span>
                                        <span
                                            class="text-xl font-bold text-gray-900">${{ number_format($pedido->subtotal, 2) }}</span>
                                    </div>

                                    <div class="flex gap-3">
                                        <x-ui.button
                                            class="flex-1"
                                            variant="success"
                                            wire:click="aceptarPedido({{ $pedido->id }})"
                                        >
                                            <i class="bxf bx-check text-xl"></i>
                                            Aceptar
                                        </x-ui.button>
                                        <x-ui.button
                                            class="flex-1"
                                            variant="secondary"
                                            wire:click="rechazarPedido({{ $pedido->id }})"
                                        >
                                            Rechazar
                                        </x-ui.button>
                                    </div>
                                </x-ui.card>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- PESTAÑA: EN COCINA --}}
                    @if (blank($pedidosAceptados) || $pedidosAceptados->isEmpty())
                        <div
                            class="my-4 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-10 text-center">
                            <div
                                class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-4xl text-blue-500 shadow-sm">
                                <i class="bxf bx-package"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Cocina sin pendientes</h3>
                            <p class="mt-2 max-w-[250px] text-sm text-gray-500">No hay pedidos preparándose en este momento.
                            </p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($pedidosAceptados as $pedido)
                                @php
                                    $deliveryStatus = $pedido->delivery_status;

                                    $statusMap = [
                                        DeliveryStatus::PENDING->value => [
                                            'label' => 'Cocina',
                                            'color' => 'blue',
                                            'pulse' => true,
                                        ],
                                        DeliveryStatus::WAITING_DRIVER->value => [
                                            'label' => 'Buscando',
                                            'color' => 'amber',
                                            'pulse' => true,
                                        ],
                                        DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT->value => [
                                            'label' => 'En Camino',
                                            'color' => 'indigo',
                                            'pulse' => true,
                                        ],
                                        DeliveryStatus::PICKED_UP->value => [
                                            'label' => 'Recogido',
                                            'color' => 'gray',
                                            'pulse' => false,
                                        ],
                                        DeliveryStatus::ON_THE_WAY->value => [
                                            'label' => 'En Ruta',
                                            'color' => 'sky',
                                            'pulse' => true,
                                        ],
                                        DeliveryStatus::DELIVERED->value => [
                                            'label' => 'Entregado',
                                            'color' => 'green',
                                            'pulse' => false,
                                        ],
                                    ];

                                    $statusKey = $deliveryStatus instanceof DeliveryStatus ? $deliveryStatus->value : $deliveryStatus;

                                    $statusDetails = $statusMap[$statusKey ?? ''] ?? [
                                        'label' => 'Estado Desconocido',
                                        'color' => 'gray',
                                        'pulse' => false,
                                    ];

                                    $isPendingDriver = $deliveryStatus === DeliveryStatus::PENDING;
                                @endphp

                                <x-ui.card
                                    class="{{ !$isPendingDriver ? 'opacity-90 border-indigo-100 bg-gray-50/50 shadow-none' : '' }} transition-all duration-300"
                                    wire:key="accepted-order-{{ $pedido->id }}"
                                >
                                    <div class="mb-4 flex items-start justify-between gap-6 border-b border-gray-100 pb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-900">
                                                <span class="-mb-1 text-[10px] uppercase text-gray-400">#</span>
                                                <span class="text-base leading-none">{{ $pedido->id }}</span>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900">
                                                    {{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                                <p
                                                    class="mt-0.5 flex items-center gap-1 text-xs font-semibold text-gray-500">
                                                    <i class="bxf bx-phone text-gray-400"></i>
                                                    {{ $pedido->customer_phone ?? 'Sin teléfono' }}
                                                </p>
                                            </div>
                                        </div>
                                        <x-ui.badge
                                            class="flex-row-reverse text-end"
                                            :color="$statusDetails['color']"
                                            :pulse="$statusDetails['pulse']"
                                        >
                                            {{ $statusDetails['label'] }}
                                        </x-ui.badge>
                                    </div>

                                    <div class="mb-4 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                        <div class="divide-y divide-gray-100">
                                            @foreach ($pedido->items ?? [] as $item)
                                                <x-ui.product-item
                                                    :quantity="$item->quantity"
                                                    :name="$item->product_name_snapshot"
                                                    :price="$item->subtotal"
                                                />
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($pedido->special_instructions)
                                        <div
                                            class="mb-4 flex items-start gap-2 rounded-xl border border-amber-100/50 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                                            <i class="bxf bx-info-circle mt-0.5 text-base text-amber-500"></i>
                                            <p class="leading-relaxed">{{ $pedido->special_instructions }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-2">
                                        <span class="text-sm font-bold text-gray-500">Total</span>
                                        <span
                                            class="text-xl font-bold text-gray-900">${{ number_format($pedido->subtotal, 2) }}</span>
                                    </div>

                                    <div>
                                        @if ($pedido->delivery_status === DeliveryStatus::PENDING)
                                            <x-ui.button
                                                class="w-full"
                                                variant="primary"
                                                wire:click="llamarRepartidor({{ $pedido->id }})"
                                            >
                                                <i class="bxf bx-rocket text-xl"></i>
                                                Llamar Repartidor
                                            </x-ui.button>
                                        @elseif ($pedido->delivery_status === DeliveryStatus::WAITING_DRIVER)
                                            <div
                                                class="flex items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-3 py-4 text-center text-sm font-bold text-amber-700">
                                                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                                                Buscando repartidor...
                                            </div>
                                        @elseif ($pedido->delivery_status === DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT)
                                            <div
                                                class="flex items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-4 text-center text-sm font-bold text-indigo-700">
                                                <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500"></span>
                                                Repartidor en camino al local
                                            </div>
                                        @elseif (in_array($pedido->delivery_status, [
                                                DeliveryStatus::PICKED_UP,
                                                DeliveryStatus::ON_THE_WAY,
                                            ], true))
                                            <div
                                                class="flex items-center justify-center gap-2 rounded-2xl border border-sky-200 bg-sky-50 px-3 py-4 text-center text-sm font-bold text-sky-700">
                                                <i class="bxf bx-cycling text-xl"></i>
                                                En ruta al cliente
                                            </div>
                                        @endif
                                    </div>
                                </x-ui.card>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </x-ui.page-section>
    </div>
@endsection