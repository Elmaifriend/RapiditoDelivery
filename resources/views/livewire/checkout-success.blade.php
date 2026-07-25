<div class="flex flex-col pb-8">

    {{-- CABECERA DE ÉXITO --}}
    <div class="bg-white border-b border-gray-100 px-4 pt-12 pb-8 text-center space-y-4">
        <div class="max-w-md mx-auto flex flex-col items-center space-y-4">
            {{-- Icono animado de Checkmark --}}
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-500 animate-bounce">
                <i class="bxf bx-check text-4xl"></i>
            </div>
            
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">¡Pedido Realizado!</h1>
                <p class="text-sm font-bold text-green-600 flex items-center justify-center gap-1.5">
                    <i class="bxf bxl-whatsapp text-lg"></i>
                    En seguida nos comunicamos por WA
                </p>
            </div>

            <p class="text-xs text-gray-500 max-w-xs leading-relaxed font-semibold">
                Hemos recibido tu orden correctamente. Nuestro equipo ya está coordinando los detalles para que tu entrega sea lo más rápida posible.
            </p>

            {{-- Número de orden destacado --}}
            <div class="inline-block bg-gray-100 px-4 py-1.5 rounded-full">
                <p class="text-xs font-bold text-gray-700 tracking-wider uppercase">
                    Orden #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto w-full flex flex-col gap-4 px-4 pt-4">
        
        {{-- SECCIÓN: DATOS DE LA TIENDA --}}
        @if($order->business)
            <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                    <i class="bxf bx-store-alt text-base text-gray-400"></i>
                    Datos de la tienda
                </h3>
                <div class="space-y-1">
                    <p class="text-sm font-extrabold text-gray-800">{{ $order->business->name }}</p>
                    @if($order->business->address)
                        <p class="text-xs font-semibold text-gray-500">{{ $order->business->address }}</p>
                    @endif
                    @if($order->business->phone)
                        <p class="text-xs font-semibold text-gray-500">Tel: {{ $order->business->phone }}</p>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- SECCIÓN: DATOS DE CONTACTO --}}
        <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <i class="bxf bx-user text-base text-gray-400"></i>
                Datos del cliente
            </h3>
            <div class="space-y-1">
                <p class="text-sm font-extrabold text-gray-800">{{ $order->customer_name }}</p>
                <p class="text-xs font-semibold text-gray-500">{{ $order->customer_phone }}</p>
            </div>
        </div>

        {{-- SECCIÓN: DIRECCIÓN DE ENTREGA --}}
        <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <i class="bxf bx-map text-base text-gray-400"></i>
                Dirección de entrega
            </h3>
            <div class="space-y-3">
                @php 
                    $dropoff = $order->dropoffLocations->first(); 
                @endphp
                
                <p class="text-sm font-extrabold text-gray-800 leading-snug">
                    {{ $dropoff?->formatted_address ?? $dropoff?->address_line ?? 'Dirección registrada' }}
                </p>
                
                @if($dropoff?->reference)
                    <div class="bg-amber-50/70 border border-amber-100/50 rounded-xl p-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Referencia aportada:</p>
                        <p class="text-xs text-amber-900 font-semibold mt-0.5">{{ $dropoff->reference }}</p>
                    </div>
                @endif

                @if($order->special_instructions)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Instrucciones especiales:</p>
                        <p class="text-xs text-gray-700 font-semibold mt-0.5">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECCIÓN: MÉTODO DE PAGO --}}
        <div class="flex items-center justify-between shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-50 text-gray-700">
                    @if($order->payment_method->value === 'card')
                        <i class="bxf bx-credit-card text-base"></i>
                    @else
                        <i class="bxf bx-dollar text-base"></i>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Método de pago</h3>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                        {{ $order->payment_method->value === 'card' ? 'Pago con Tarjeta (Terminal)' : 'Pago en Efectivo' }}
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
                Pendiente de pago
            </span>
        </div>

        {{-- SECCIÓN: RESUMEN DE PRODUCTOS --}}
        <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <i class="bxf bx-receipt text-base text-gray-400"></i>
                Resumen de productos
            </h3>

            {{-- Listado de productos --}}
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-red-50 text-red-500 text-xs font-bold">
                                {{ $item->quantity }}x
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-sm font-extrabold text-gray-800">{{ $item->product_name_snapshot }}</p>
                                @if($item->product_description_snapshot)
                                    <p class="text-[10px] font-medium text-gray-400 italic">{{ $item->product_description_snapshot }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-sm font-bold font-mono text-gray-700">
                            ${{ number_format($item->subtotal, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Desglose totalizador --}}
            <div class="pt-4 bg-gray-50/50 -mx-4 -mb-4 p-4 rounded-b-2xl border-t border-gray-100 space-y-2">
                <div class="flex justify-between text-xs font-extrabold text-gray-500">
                    <span>Subtotal</span>
                    <span class="font-mono text-gray-700">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                
                <div class="flex justify-between text-xs font-extrabold text-gray-500">
                    <span>Envío a domicilio</span>
                    <span class="font-mono text-gray-700">${{ number_format($order->delivery_fee, 2) }}</span>
                </div>

                <div class="flex justify-between items-center text-sm font-bold text-gray-900 pt-3 border-t border-gray-100">
                    <span>Total de la Orden</span>
                    <span class="font-mono text-lg text-red-600 font-bold">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- BOTÓN DE RETORNO AL INICIO --}}
        <div class="pt-4">
            <a href="/" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 p-4 font-bold text-white transition-all active:scale-[0.98] shadow-lg hover:bg-gray-800 text-sm">
                <i class="bxf bx-home-alt text-base"></i>
                Volver a la Tienda
            </a>
        </div>

    </div>
</div>