<div class="flex flex-col min-h-screen bg-gray-50/50 pb-20">

    {{-- CABECERA / IDENTIFICADOR DE COCINA --}}
    <div class="bg-gray-900 text-white px-4 py-2 text-center text-xs font-semibold flex items-center justify-between shadow-sm">
        <span class="truncate">Panel de Cocina: <strong class="text-amber-400">Negocio #{{ $businessId }}</strong></span>
        <span class="inline-flex items-center gap-1 bg-gray-800 px-2 py-0.5 rounded text-[10px] text-gray-300">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Sistema Activo
        </span>
    </div>

    {{-- ALERTA DE MENSAJE FLASH DE SESIÓN --}}
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto w-full px-4 pt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto w-full px-4 pt-6 space-y-6">

        {{-- ENCABEZADO Y PESTAÑAS DE NAVEGACIÓN --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-2 md:p-3 rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-2">
                <h1 class="text-xl font-black tracking-tight text-gray-900">Control de Cocina</h1>
                <p class="text-xs text-gray-400 font-medium">Gestión de órdenes entrantes y estado de producción</p>
            </div>

            {{-- SELECTOR DE PESTAÑAS ESTILO NATIVE / PILL --}}
            <div class="flex bg-gray-100/80 p-1 rounded-xl gap-1">
                <button 
                    wire:click="changeTab('nuevos')" 
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-black transition-all duration-200 flex items-center justify-center gap-2 {{ $activeTab === 'nuevos' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <span>Nuevos Pedidos</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'nuevos' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ count($pedidosNuevos) }}
                    </span>
                </button>

                <button 
                    wire:click="changeTab('preparacion')" 
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-black transition-all duration-200 flex items-center justify-center gap-2 {{ $activeTab === 'preparacion' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <span>En Preparación</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'preparacion' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' }}">
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
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 max-w-md mx-auto my-8 space-y-3">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-500 text-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Sin órdenes por aceptar</h3>
                        <p class="text-xs text-gray-400 max-w-xs mx-auto">No hay pedidos pendientes en este momento. Las nuevas solicitudes de clientes aparecerán automáticamente aquí.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($pedidosNuevos as $pedido)
                            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm flex flex-col justify-between transition-all duration-200 hover:border-gray-200 hover:shadow-md">
                                <div class="p-5 space-y-4">
                                    {{-- Cabecera Tarjeta --}}
                                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                                        <div class="inline-block bg-gray-100 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-black tracking-wider text-gray-700 uppercase">
                                                ID: #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        <span class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-800 text-[10px] font-black uppercase px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Por Aceptar
                                        </span>
                                    </div>

                                    {{-- Datos del Cliente --}}
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                        <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            Tel: {{ $pedido->customer_phone ?? 'N/A' }}
                                        </p>
                                    </div>

                                    {{-- Listado de Ítems --}}
                                    <div class="bg-gray-50/60 rounded-xl p-3 border border-gray-100 space-y-2">
                                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Productos Solicitados</p>
                                        <div class="divide-y divide-gray-100">
                                            @foreach($pedido->items as $item)
                                                <div class="py-1.5 flex justify-between items-center text-xs">
                                                    <span class="text-gray-800 font-medium">
                                                        <strong class="font-black text-gray-900 bg-gray-200/60 px-1.5 py-0.5 rounded mr-1">{{ $item->quantity }}x</strong> 
                                                        {{ $item->name }}
                                                    </span>
                                                    <span class="font-bold text-gray-500">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Notas Especiales --}}
                                    @if($pedido->special_instructions)
                                        <div class="bg-amber-50/70 border border-amber-200/60 p-2.5 rounded-xl text-xs text-amber-900 space-y-0.5">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-amber-700">Notas Especiales:</p>
                                            <p class="leading-relaxed">{{ $pedido->special_instructions }}</p>
                                        </div>
                                    @endif

                                    {{-- Total --}}
                                    <div class="flex justify-between items-end pt-1">
                                        <span class="text-xs font-bold text-gray-400">Total a cobrar:</span>
                                        <span class="text-xl font-black text-gray-900">${{ number_format($pedido->total, 2) }}</span>
                                    </div>
                                </div>

                                {{-- Botones de Acción --}}
                                <div class="bg-gray-50 p-3 border-t border-gray-100 flex gap-2">
                                    <button 
                                        wire:click="aceptarPedido({{ $pedido->id }})" 
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-3 rounded-xl text-xs transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Aceptar Pedido
                                    </button>
                                    <button 
                                        wire:click="rechazarPedido({{ $pedido->id }})" 
                                        class="bg-white hover:bg-red-50 text-red-600 font-bold py-2.5 px-3 rounded-xl text-xs transition-colors border border-gray-200 hover:border-red-200">
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
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 max-w-md mx-auto my-8 space-y-3">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-500 text-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Cocina limpia y sin pendientes</h3>
                        <p class="text-xs text-gray-400 max-w-xs mx-auto">No hay pedidos preparándose en este momento.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($pedidosAceptados as $pedido)
                            <div wire:key="order-{{ $pedido->id }}" 
                                class="bg-white rounded-2xl border overflow-hidden shadow-sm flex flex-col justify-between transition-all duration-300 {{ $pedido->delivery_status->value !== 'pending' ? 'opacity-75 border-indigo-100 bg-gray-50/30' : 'border-gray-100 hover:shadow-md' }}">
                                
                                <div class="p-5 space-y-4">
                                    {{-- Cabecera Tarjeta --}}
                                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                                        <div class="inline-block bg-gray-100 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-black tracking-wider text-gray-700 uppercase">
                                                ID: #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        
                                        @if($pedido->delivery_status->value !== 'pending')
                                            <span class="inline-flex items-center gap-1 bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span> Esperando Repartidor
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-800 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> En Cocina
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Datos del Cliente, Productos, Notas y Total permanecen igual --}}
                                    ...
                                </div>

                                {{-- Acciones: Disparo de repartidor --}}
                                <div class="bg-gray-50 p-3 border-t border-gray-100">
                                    @if($pedido->delivery_status->value === 'pending')
                                        <button 
                                            wire:click="llamarRepartidor({{ $pedido->id }})" 
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-3 rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Llamar Repartidor
                                        </button>
                                    @else
                                        <div class="text-center py-2.5 px-3 bg-indigo-50 text-indigo-800 rounded-xl text-xs font-black border border-indigo-100 flex items-center justify-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                            Repartidor Solicitado ({{ $pedido->delivery_status->value }})
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