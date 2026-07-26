<div class="flex flex-col pb-8">

    {{-- CABECERA / IDENTIFICADOR DE COCINA --}}
    <div class="bg-white text-gray-800 px-4 py-3.5 text-center text-xs font-semibold flex items-center justify-between shadow-xs border-b border-gray-100">
        <span class="truncate">Panel de Cocina: <strong class="text-red-500">Negocio #{{ $businessId }}</strong></span>
        <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-[10px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Sistema Activo
        </span>
    </div>

    {{-- ALERTA DE MENSAJE FLASH DE SESIÓN --}}
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto w-full px-4 pt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="bxf bx-check-circle text-base text-emerald-600 flex-shrink-0"></i>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto w-full px-4 pt-6 space-y-6">

        {{-- ENCABEZADO Y PESTAÑAS DE NAVEGACIÓN --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 rounded-3xl border border-gray-100 shadow-xs">
            <div class="space-y-1">
                <h1 class="text-lg font-bold tracking-tight text-gray-900">Control de Cocina</h1>
                <p class="text-xs text-gray-400 font-semibold">Gestión de órdenes entrantes y estado de producción</p>
            </div>

            {{-- SELECTOR DE PESTAÑAS ESTILO NATIVE / PILL --}}
            <div class="flex bg-gray-50 p-1 rounded-2xl border border-gray-100 gap-1 w-full md:w-auto">
                <button 
                    wire:click="changeTab('nuevos')" 
                    class="flex-1 md:flex-none px-2.5 py-2 md:px-4 md:py-2.5 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'nuevos' ? 'bg-white text-red-500 shadow-xs border border-gray-100' : 'text-gray-500 hover:text-gray-700' }}">
                    <span>Nuevos<span class="hidden sm:inline"> Pedidos</span></span>
                    <span class="px-1.5 py-0.5 rounded-lg text-[10px] font-bold {{ $activeTab === 'nuevos' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ count($pedidosNuevos) }}
                    </span>
                </button>

                <button 
                    wire:click="changeTab('preparacion')" 
                    class="flex-1 md:flex-none px-2.5 py-2 md:px-4 md:py-2.5 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'preparacion' ? 'bg-white text-red-500 shadow-xs border border-gray-100' : 'text-gray-500 hover:text-gray-700' }}">
                    <span>En Cocina<span class="hidden sm:inline"> / Preparación</span></span>
                    <span class="px-1.5 py-0.5 rounded-lg text-[10px] font-bold {{ $activeTab === 'preparacion' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ count($pedidosAceptados) }}
                    </span>
                </button>
            </div>
        </div>

        {{-- CONTENIDO DE LAS PESTAÑAS --}}
        <div>
            @if($activeTab === 'nuevos')
                {{-- ======================================================== --}}
                {{-- PESTAÑA: NUEVOS PEDIDOS POR ACEPTAR --}}
                {{-- ======================================================== --}}
                @if($pedidosNuevos->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 max-w-md mx-auto my-8 space-y-3 shadow-xs">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-500 text-2xl">
                            <i class="bxf bx-bell text-4xl text-amber-500 animate-swing"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Sin órdenes por aceptar</h3>
                        <p class="text-xs text-gray-400 max-w-xs mx-auto">No hay pedidos pendientes en este momento. Las nuevas solicitudes aparecerán aquí.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($pedidosNuevos as $pedido)
                            <div class="flex flex-col justify-between gap-4 shadow-xs rounded-3xl border border-gray-100 bg-white p-5 hover:border-gray-200 hover:shadow-sm transition-all duration-200">
                                <div class="space-y-4">
                                    {{-- Cabecera Tarjeta --}}
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-3.5">
                                        <div class="inline-block bg-red-50 border border-red-100 px-3 py-1.5 rounded-xl">
                                            <span class="text-sm font-extrabold tracking-wider text-red-600">
                                                #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        <span class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-800 text-[10px] font-bold uppercase px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Por Aceptar
                                        </span>
                                    </div>

                                    {{-- Datos del Cliente --}}
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Cliente</h4>
                                        <h3 class="font-bold text-gray-800 text-sm leading-snug mt-1">{{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                        <p class="text-xs font-semibold text-gray-500 flex items-center gap-1.5 mt-1">
                                            <i class="bxf bx-phone text-xs text-gray-400"></i>
                                            Tel: {{ $pedido->customer_phone ?? 'N/A' }}
                                        </p>
                                    </div>

                                    {{-- Listado de Ítems --}}
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-3">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Productos Solicitados</p>
                                        <div class="divide-y divide-gray-100">
                                            @foreach($pedido->items as $item)
                                                <div class="py-2 flex justify-between items-center text-xs first:pt-0 last:pb-0">
                                                    <span class="text-gray-800 font-semibold">
                                                        <strong class="font-bold text-gray-900 bg-gray-200/50 px-1.5 py-0.5 rounded mr-1.5">{{ $item->quantity }}x</strong> 
                                                        {{ $item->product_name_snapshot }}
                                                    </span>
                                                    <span class="font-bold text-gray-700 font-mono">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Notas Especiales --}}
                                    @if($pedido->special_instructions)
                                        <div class="bg-amber-50/70 border border-amber-100/50 p-3 rounded-2xl text-xs text-amber-900 space-y-0.5">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Notas Especiales:</p>
                                            <p class="leading-relaxed font-semibold">{{ $pedido->special_instructions }}</p>
                                        </div>
                                    @endif

                                    {{-- Total --}}
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total:</span>
                                        <span class="text-lg font-bold text-red-500 font-mono">${{ number_format($pedido->subtotal, 2) }}</span>
                                    </div>
                                </div>

                                {{-- Botones de Acción --}}
                                <div class="flex gap-2 pt-2">
                                    <button 
                                        wire:click="aceptarPedido({{ $pedido->id }})" 
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-3 rounded-2xl text-xs transition-colors shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                        <i class="bxf bx-check text-base"></i>
                                        Aceptar
                                    </button>
                                    <button 
                                        wire:click="rechazarPedido({{ $pedido->id }})" 
                                        class="bg-white hover:bg-red-50 text-red-500 font-bold py-3 px-4 rounded-2xl text-xs transition-colors border border-gray-200 hover:border-red-200 cursor-pointer">
                                        Rechazar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            @else
                {{-- ======================================================== --}}
                {{-- PESTAÑA: EN COCINA / PREPARACIÓN --}}
                {{-- ======================================================== --}}
                @if($pedidosAceptados->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 max-w-md mx-auto my-8 space-y-3 shadow-xs">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-500 text-2xl">
                            <i class="bxf bx-package text-4xl text-blue-500"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Cocina limpia y sin pendientes</h3>
                        <p class="text-xs text-gray-400 max-w-xs mx-auto">No hay pedidos preparándose en este momento.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($pedidosAceptados as $pedido)
                            @php
                                $deliveryStatus = $pedido->delivery_status;
                                $statusMap = [
                                    \App\Enums\DeliveryStatus::PENDING->value => [
                                        'label' => 'En Cocina',
                                        'class' => 'bg-blue-50 border-blue-200 text-blue-800',
                                        'dot' => 'bg-blue-500 animate-pulse',
                                    ],
                                    \App\Enums\DeliveryStatus::WAITING_DRIVER->value => [
                                        'label' => 'Buscando Repartidor',
                                        'class' => 'bg-amber-50 border-amber-200 text-amber-800',
                                        'dot' => 'bg-amber-500 animate-ping',
                                    ],
                                    \App\Enums\DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT->value => [
                                        'label' => 'Repartidor en Camino',
                                        'class' => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                                        'dot' => 'bg-indigo-500 animate-pulse',
                                    ],
                                    \App\Enums\DeliveryStatus::PICKED_UP->value => [
                                        'label' => 'Pedido Recogido',
                                        'class' => 'bg-gray-100 border-gray-200 text-gray-700',
                                        'dot' => 'bg-gray-500',
                                    ],
                                    \App\Enums\DeliveryStatus::ON_THE_WAY->value => [
                                        'label' => 'En Ruta al Cliente',
                                        'class' => 'bg-sky-50 border-sky-200 text-sky-800',
                                        'dot' => 'bg-sky-500 animate-pulse',
                                    ],
                                    \App\Enums\DeliveryStatus::DELIVERED->value => [
                                        'label' => 'Entregado',
                                        'class' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                        'dot' => 'bg-emerald-500',
                                    ],
                                ];
                                $statusDetails = $statusMap[$deliveryStatus->value] ?? [
                                    'label' => 'Estado Desconocido',
                                    'class' => 'bg-gray-50 border-gray-200 text-gray-600',
                                    'dot' => 'bg-gray-400',
                                ];
                            @endphp
                            <div wire:key="order-{{ $pedido->id }}" 
                                class="flex flex-col justify-between gap-4 shadow-xs rounded-3xl border p-5 transition-all duration-300 {{ $pedido->delivery_status->value !== 'pending' ? 'opacity-75 border-indigo-100 bg-gray-50/40 shadow-none' : 'border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm' }}">
                                
                                <div class="space-y-4">
                                    {{-- Cabecera Tarjeta --}}
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-3.5">
                                        <div class="inline-block bg-red-50 border border-red-100 px-3 py-1.5 rounded-xl">
                                            <span class="text-sm font-extrabold tracking-wider text-red-600">
                                                #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        
                                        <span class="inline-flex items-center gap-1 border text-[10px] font-bold uppercase px-2.5 py-1 rounded-full {{ $statusDetails['class'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusDetails['dot'] }}"></span> {{ $statusDetails['label'] }}
                                        </span>
                                    </div>

                                    {{-- Datos del Cliente --}}
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Cliente</h4>
                                        <h3 class="font-bold text-gray-800 text-sm leading-snug mt-1">{{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                        <p class="text-xs font-semibold text-gray-500 flex items-center gap-1.5 mt-1">
                                            <i class="bxf bx-phone text-xs text-gray-400"></i>
                                            Tel: {{ $pedido->customer_phone ?? 'N/A' }}
                                        </p>
                                    </div>

                                    {{-- Listado de Ítems --}}
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-3">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Productos Solicitados</p>
                                        <div class="divide-y divide-gray-100">
                                            @foreach($pedido->items as $item)
                                                <div class="py-2 flex justify-between items-center text-xs first:pt-0 last:pb-0">
                                                    <span class="text-gray-800 font-semibold">
                                                        <strong class="font-bold text-gray-900 bg-gray-200/50 px-1.5 py-0.5 rounded mr-1.5">{{ $item->quantity }}x</strong> 
                                                        {{ $item->product_name_snapshot }}
                                                    </span>
                                                    <span class="font-bold text-gray-700 font-mono">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Notas Especiales --}}
                                    @if($pedido->special_instructions)
                                        <div class="bg-amber-50/70 border border-amber-100/50 p-3 rounded-2xl text-xs text-amber-900 space-y-0.5">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Notas Especiales:</p>
                                            <p class="leading-relaxed font-semibold">{{ $pedido->special_instructions }}</p>
                                        </div>
                                    @endif

                                    {{-- Total --}}
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total:</span>
                                        <span class="text-lg font-bold text-red-500 font-mono">${{ number_format($pedido->subtotal, 2) }}</span>
                                    </div>
                                </div>

                                {{-- Acciones: Disparo de repartidor --}}
                                <div class="pt-2">
                                    @if($pedido->delivery_status->value === \App\Enums\DeliveryStatus::PENDING->value)
                                        <button 
                                            wire:click="llamarRepartidor({{ $pedido->id }})" 
                                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-3 rounded-2xl text-xs transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                                            <i class="bxf bx-rocket text-base"></i>
                                            Llamar Repartidor
                                        </button>
                                    @elseif($pedido->delivery_status->value === \App\Enums\DeliveryStatus::WAITING_DRIVER->value)
                                        <div class="text-center py-3 px-3 bg-amber-50 text-amber-800 rounded-2xl text-xs font-bold border border-amber-100 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            Buscando repartidor...
                                        </div>
                                    @elseif($pedido->delivery_status->value === \App\Enums\DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT->value)
                                        <div class="text-center py-3 px-3 bg-indigo-50 text-indigo-800 rounded-2xl text-xs font-bold border border-indigo-100 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                            Repartidor en camino a tienda
                                        </div>
                                    @elseif($pedido->delivery_status->value === \App\Enums\DeliveryStatus::PICKED_UP->value)
                                        <div class="text-center py-3 px-3 bg-gray-100 text-gray-700 rounded-2xl text-xs font-bold border border-gray-200 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                            Recogido por repartidor
                                        </div>
                                    @elseif($pedido->delivery_status->value === \App\Enums\DeliveryStatus::ON_THE_WAY->value)
                                        <div class="text-center py-3 px-3 bg-sky-50 text-sky-800 rounded-2xl text-xs font-bold border border-sky-100 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                            Pedido en ruta al cliente
                                        </div>
                                    @elseif($pedido->delivery_status->value === \App\Enums\DeliveryStatus::DELIVERED->value)
                                        <div class="text-center py-3 px-3 bg-emerald-50 text-emerald-800 rounded-2xl text-xs font-bold border border-emerald-100 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Pedido entregado con éxito
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>