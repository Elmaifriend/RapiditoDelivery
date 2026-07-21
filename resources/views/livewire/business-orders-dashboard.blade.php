<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Encabezado del Dashboard -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Panel de Control de Cocina (Negocio #{{ $businessId }})</h1>
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <!-- Pestañas de Navegación -->
        <div class="flex border-b border-gray-200 mb-6">
            <button 
                wire:click="changeTab('nuevos')" 
                class="py-2 px-4 font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'nuevos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Nuevos Pedidos por Aceptar ({{ count($pedidosNuevos) }})
            </button>
            <button 
                wire:click="changeTab('preparacion')" 
                class="py-2 px-4 font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'preparacion' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                En Cocina / Preparación ({{ count($pedidosAceptados) }})
            </button>
        </div>

        <!-- Contenido de las Pestañas -->
        <div>
            @if($activeTab === 'nuevos')
                <!-- PESTAÑA: NUEVOS PEDIDOS -->
                @if($pedidosNuevos->isEmpty())
                    <div class="bg-white p-8 rounded-lg shadow-sm text-center text-gray-500">
                        No hay pedidos nuevos por aceptar en este momento.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pedidosNuevos as $pedido)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="text-sm font-semibold text-gray-500">ID: #{{ $pedido->id }}</span>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">Por Aceptar</span>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                        <p class="text-xs text-gray-500">Tel: {{ $pedido->customer_phone ?? 'N/A' }}</p>
                                    </div>

                                    <!-- Listado de Ítems -->
                                    <div class="border-t border-b border-gray-100 py-3 my-3">
                                        <p class="text-xs font-semibold text-gray-400 mb-2 uppercase">Productos</p>
                                        <div class="space-y-1">
                                            @foreach($pedido->items as $item)
                                                <div class="flex justify-between text-sm text-gray-700">
                                                    <span><strong class="text-gray-900">{{ $item->quantity }}x</strong> {{ $item->name }}</span>
                                                    <span class="text-gray-500">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if($pedido->special_instructions)
                                        <div class="bg-amber-50 border-l-4 border-amber-400 p-2 text-sm text-amber-700 rounded mb-3">
                                            <strong>Notas:</strong> {{ $pedido->special_instructions }}
                                        </div>
                                    @endif

                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">Total:</span>
                                        <span class="text-xl font-bold text-gray-900 block">${{ number_format($pedido->total, 2) }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex gap-2">
                                    <button 
                                        wire:click="aceptarPedido({{ $pedido->id }})" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded text-sm transition-colors shadow-sm">
                                        Aceptar
                                    </button>
                                    <button 
                                        wire:click="rechazarPedido({{ $pedido->id }})" 
                                        class="bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 px-3 rounded text-sm transition-colors border border-red-200">
                                        Rechazar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <!-- PESTAÑA: EN COCINA / PREPARACIÓN -->
                @if($pedidosAceptados->isEmpty())
                    <div class="bg-white p-8 rounded-lg shadow-sm text-center text-gray-500">
                        No hay pedidos en preparación actualmente.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pedidosAceptados as $pedido)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="text-sm font-semibold text-gray-500">ID: #{{ $pedido->id }}</span>
                                        
                                        @if($pedido->lifecycle_status->value === 'accepted_by_restaurant')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">Aceptado</span>
                                        @elseif($pedido->lifecycle_status->value === 'in_preparation')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded">En Cocina</span>
                                        @elseif($pedido->lifecycle_status->value === 'ready')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">¡Listo para Entrega!</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $pedido->customer_name ?? 'Cliente Invitado' }}</h3>
                                        <p class="text-xs text-gray-500">Tel: {{ $pedido->customer_phone ?? 'N/A' }}</p>
                                    </div>

                                    <!-- Listado de Ítems -->
                                    <div class="border-t border-b border-gray-100 py-3 my-3">
                                        <p class="text-xs font-semibold text-gray-400 mb-2 uppercase">Productos</p>
                                        <div class="space-y-1">
                                            @foreach($pedido->items as $item)
                                                <div class="flex justify-between text-sm text-gray-700">
                                                    <span><strong class="text-gray-900">{{ $item->quantity }}x</strong> {{ $item->name }}</span>
                                                    <span class="text-gray-500">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if($pedido->special_instructions)
                                        <div class="bg-amber-50 border-l-4 border-amber-400 p-2 text-sm text-amber-700 rounded mb-3">
                                            <strong>Notas:</strong> {{ $pedido->special_instructions }}
                                        </div>
                                    @endif

                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">Total:</span>
                                        <span class="text-xl font-bold text-gray-900 block">${{ number_format($pedido->total, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Acciones: Un solo botón de disparo -->
                                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
                                    @if($pedido->delivery_status->value === 'pending')
                                        <button 
                                            wire:click="llamarRepartidor({{ $pedido->id }})" 
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-3 rounded text-sm transition-colors shadow-sm">
                                            Llamar Repartidor
                                        </button>
                                    @else
                                        <div class="text-center py-2 bg-indigo-50 text-indigo-700 rounded text-xs font-semibold border border-indigo-100">
                                            Repartidor Solicitado (Estado: {{ $pedido->delivery_status->value }})
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