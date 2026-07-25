<div class="flex flex-col">

    {{-- CABECERA DE ÉXITO --}}
    <div class="bg-white border-b border-gray-100 px-4 pt-12 pb-8 text-center space-y-4">
        <div class="max-w-md mx-auto flex flex-col items-center space-y-4">
            {{-- Icono animado de Checkmark --}}
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-500 text-2xl animate-bounce">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <div class="space-y-1">
                <h1 class="text-2xl font-black tracking-tight text-gray-900">¡Pedido Realizado!</h1>
                <p class="text-sm font-bold text-green-600 flex items-center justify-center gap-1.5">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    En seguida nos comunicamos por WA
                </p>
            </div>

            <p class="text-xs text-gray-500 max-w-xs leading-relaxed">
                Hemos recibido tu orden correctamente. Nuestro equipo ya está coordinando los detalles para que tu entrega sea lo más rápida posible.
            </p>

            {{-- Número de orden destacado --}}
            <div class="inline-block bg-gray-100 px-4 py-1.5 rounded-full">
                <p class="text-xs font-black text-gray-700 tracking-wider uppercase">
                    Orden #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto w-full flex flex-col gap-4 px-4 pt-4">
        
        {{-- SECCIÓN: DATOS DE CONTACTO --}}
        <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
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
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Método de pago</h3>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                        {{ $order->payment_method->value === 'card' ? 'Pago con Tarjeta (Terminal)' : 'Pago en Efectivo' }}
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
                Pendiente de pago
            </span>
        </div>

        {{-- SECCIÓN: RESUMEN DE PRODUCTOS --}}
        <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Resumen de productos
            </h3>

            {{-- Listado de productos --}}
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-red-50 text-red-500 text-xs font-black">
                                {{ $item->quantity }}x
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-sm font-extrabold text-gray-800">{{ $item->name ?? $item->menuItem?->name }}</p>
                                @if($item->notes)
                                    <p class="text-[10px] font-medium text-gray-400 italic">"{{ $item->notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-sm font-bold font-mono text-gray-700">
                            ${{ number_format($item->subtotal ?? ($item->price * $item->quantity), 2) }}
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

                <div class="flex justify-between items-center text-sm font-black text-gray-900 pt-3 border-t border-gray-100">
                    <span>Total de la Orden</span>
                    <span class="font-mono text-lg text-red-600 font-black">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- BOTÓN DE RETORNO AL INICIO --}}
        <div class="pt-4">
            <a href="/" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 p-4 font-bold text-white transition-all active:scale-[0.98] shadow-lg hover:bg-gray-800 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Volver a la Tienda
            </a>
        </div>

    </div>
</div>