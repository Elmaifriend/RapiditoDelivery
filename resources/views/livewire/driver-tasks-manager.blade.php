<div class="flex flex-col min-h-screen bg-gray-50/50 pb-20">

    {{-- CABECERA / IDENTIFICADOR DEL REPARTIDOR --}}
    <div class="bg-gray-900 text-white px-4 py-2 text-center text-xs font-semibold flex items-center justify-between">
        <span class="truncate">Panel Repartidor: <strong class="text-green-400">{{ $driver->name ?? 'ID #'.$driver->id }}</strong></span>
        <span class="inline-flex items-center gap-1 bg-gray-800 px-2 py-0.5 rounded text-[10px] text-gray-300">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Activo
        </span>
    </div>

    {{-- SI NO HAY PEDIDO ASIGNADO --}}
    @if(!$currentOrder)
        <div class="bg-white px-4 pt-16 pb-12 border-b border-gray-100 text-center space-y-4 my-auto max-w-md mx-auto w-full">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-gray-400 text-3xl">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            
            <div class="space-y-1">
                <h1 class="text-xl font-black tracking-tight text-gray-900">No hay pedidos en este momento</h1>
                <p class="text-xs text-gray-500 max-w-xs mx-auto leading-relaxed">
                    Estás en línea y disponible. Tan pronto como un restaurante marque una orden lista, se te asignará automáticamente.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 bg-green-50 px-3 py-1.5 rounded-full border border-green-100">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-green-700">Esperando tareas...</span>
            </div>
        </div>

    @else
        {{-- SI HAY UN PEDIDO ASIGNADO --}}
        
        @php
            $dropoff = $currentOrder->dropoffLocations->first();
            $restaurant = $currentOrder->business;
            
            // Generación de links de Google Maps
            $restaurantAddressEnc = urlencode(($restaurant->address ?? '') . ' ' . ($restaurant->name ?? ''));
            $restaurantMapsUrl = "https://www.google.com/maps/search/?api=1&query={$restaurantAddressEnc}";

            $dropoffAddressEnc = urlencode($dropoff?->formatted_address ?? $dropoff?->address_line ?? '');
            $dropoffMapsUrl = "https://www.google.com/maps/search/?api=1&query={$dropoffAddressEnc}";
            
            // Estado de la entrega
            $isHeadingToRestaurant = $currentOrder->delivery_status === \App\Enums\DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT;
        @endphp

        {{-- CABECERA DE TAREA ACTUAL --}}
        <div class="bg-white px-4 pt-10 pb-6 border-b border-gray-100 text-center space-y-3">
            <div class="inline-block bg-gray-100 px-4 py-1.5 rounded-full">
                <p class="text-xs font-black text-gray-700 tracking-wider uppercase">
                    Orden #{{ str_pad($currentOrder->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            @if($isHeadingToRestaurant)
                <h1 class="text-2xl font-black tracking-tight text-gray-900">Ve a recoger este pedido</h1>
                <p class="text-xs font-bold text-amber-600 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    Paso 1: Dirígete al Restaurante
                </p>
            @else
                <h1 class="text-2xl font-black tracking-tight text-gray-900">Entrega este pedido</h1>
                <p class="text-xs font-bold text-blue-600 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                    Paso 2: En trayecto al cliente
                </p>
            @endif
        </div>

        <div class="max-w-md mx-auto w-full space-y-4 p-4" x-data="{ step: '{{ $isHeadingToRestaurant ? 'pickup' : 'deliver' }}' }">

            {{-- ======================================================== --}}
            {{-- ETAPA 1: IR AL RESTAURANTE --}}
            {{-- ======================================================== --}}
            @if($isHeadingToRestaurant)
                
                {{-- VISTA DE DIRECCIÓN DEL RESTAURANTE + MAPA --}}
                <div x-show="step === 'pickup'" class="space-y-4">
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-3">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Restaurante de Origen
                        </h3>
                        <div>
                            <p class="text-base font-bold text-gray-900">{{ $restaurant->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $restaurant->address }}</p>
                        </div>

                        {{-- MINIATURA VISUAL DE MAPA PREVIEW --}}
                        <div class="relative w-full h-32 bg-gray-100 rounded-xl overflow-hidden border border-gray-200/60 flex items-center justify-center group">
                            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-70"></div>
                            <div class="z-10 flex flex-col items-center gap-1">
                                <div class="p-2 bg-red-500 text-white rounded-full shadow-lg animate-bounce">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-gray-600 bg-white/90 px-2 py-0.5 rounded-md shadow-sm">Destino Restaurante</span>
                            </div>
                        </div>

                        {{-- LINK A GOOGLE MAPS GPS --}}
                        <a href="{{ $restaurantMapsUrl }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-blue-50 text-blue-600 rounded-xl font-bold text-xs border border-blue-100 hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Abrir en Google Maps GPS
                        </a>
                    </div>

                    {{-- SLIDER IPHONE PARA REVISAR ORDEN --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 text-center">Desliza cuando llegues al restaurante</p>
                        
                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            completed: false,
                            init() { this.maxSwipe = $refs.track.clientWidth - $refs.thumb.clientWidth - 8; },
                            touchStart(e) { this.startX = e.touches[0].clientX; },
                            touchMove(e) { 
                                let diff = e.touches[0].clientX - this.startX;
                                if(diff > 0 && diff <= this.maxSwipe) this.currentX = diff;
                            },
                            touchEnd() {
                                if(this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    this.completed = true;
                                    setTimeout(() => { step = 'checklist'; }, 200);
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" class="relative select-none">
                            <div x-ref="track" class="h-14 bg-gray-900 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden">
                                <span class="text-xs font-bold text-gray-300 tracking-wider uppercase opacity-80 pointer-events-none">Llegué al Restaurante &gt;&gt;</span>
                                <div x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @touchstart="touchStart" 
                                     @touchmove="touchMove" 
                                     @touchend="touchEnd"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75">
                                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUPERVISIÓN / CHECKLIST DE LA ORDEN EN RESTAURANTE --}}
                <div x-show="step === 'checklist'" x-cloak class="space-y-4">
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-3">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verificación de Productos
                        </h3>

                        <div class="divide-y divide-gray-50 border-t border-b border-gray-50">
                            @foreach($currentOrder->items as $item)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-900">{{ $item->quantity }}x {{ $item->name ?? $item->menuItem?->name }}</span>
                                    <span class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded-md">Verificado</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- REPORTES DEL CONDUCTOR --}}
                        <div class="space-y-2 pt-1">
                            <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">¿Todo está en orden?</label>
                            <div class="flex gap-2">
                                <button type="button" @click="$wire.set('allItemsCorrect', true)" :class="$wire.allItemsCorrect ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600'" class="flex-1 py-2 rounded-xl font-bold text-xs transition-colors">
                                    ✓ Todo Correcto
                                </button>
                                <button type="button" @click="$wire.set('allItemsCorrect', false)" :class="!$wire.allItemsCorrect ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600'" class="flex-1 py-2 rounded-xl font-bold text-xs transition-colors">
                                    ✕ Falta algo / Detalle
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Notas / Comentarios adicionales</label>
                            <textarea wire:model="driverNotes" rows="2" class="w-full text-xs rounded-xl border-gray-200 focus:border-gray-900 focus:ring-0 p-2.5 bg-gray-50" placeholder="Escribe aquí si hubo algún detalle con el empaque o producto..."></textarea>
                        </div>
                    </div>

                    {{-- SLIDER IPHONE DE CONFIRMACIÓN DE RECOGIDA --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 text-center">Desliza para Iniciar Viaje al Cliente</p>

                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            init() { this.maxSwipe = $refs.track.clientWidth - $refs.thumb.clientWidth - 8; },
                            touchStart(e) { this.startX = e.touches[0].clientX; },
                            touchMove(e) { 
                                let diff = e.touches[0].clientX - this.startX;
                                if(diff > 0 && diff <= this.maxSwipe) this.currentX = diff;
                            },
                            touchEnd() {
                                if(this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    $wire.markAsPickedUp();
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" class="relative select-none">
                            <div x-ref="track" class="h-14 bg-green-600 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden">
                                <span class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none">Confirmar Recogida &gt;&gt;</span>
                                <div x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @touchstart="touchStart" 
                                     @touchmove="touchMove" 
                                     @touchend="touchEnd"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- ======================================================== --}}
            {{-- ETAPA 2: IR AL DOMICILIO DEL CLIENTE --}}
            {{-- ======================================================== --}}
            @else
                
                <div class="space-y-4">
                    {{-- DATOS DEL CLIENTE Y DIRECCIÓN --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-3">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Cliente
                        </h3>
                        <div>
                            <p class="text-base font-bold text-gray-900">{{ $currentOrder->customer_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $currentOrder->customer_phone }}</p>
                        </div>

                        <div class="pt-2 border-t border-gray-50 space-y-1">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-400">Dirección de Entrega</h3>
                            <p class="text-sm font-bold text-gray-900 leading-snug">
                                {{ $dropoff?->formatted_address ?? $dropoff?->address_line ?? 'Dirección registrada' }}
                            </p>
                            @if($dropoff?->reference)
                                <p class="text-xs text-amber-800 bg-amber-50 p-2 rounded-lg mt-1 font-medium">Ref: {{ $dropoff->reference }}</p>
                            @endif
                        </div>

                        {{-- MINIATURA VISUAL DE MAPA PREVIEW --}}
                        <div class="relative w-full h-32 bg-gray-100 rounded-xl overflow-hidden border border-gray-200/60 flex items-center justify-center group">
                            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-70"></div>
                            <div class="z-10 flex flex-col items-center gap-1">
                                <div class="p-2 bg-blue-500 text-white rounded-full shadow-lg animate-bounce">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-gray-600 bg-white/90 px-2 py-0.5 rounded-md shadow-sm">Casa del Cliente</span>
                            </div>
                        </div>

                        {{-- LINK A GOOGLE MAPS GPS --}}
                        <a href="{{ $dropoffMapsUrl }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-blue-50 text-blue-600 rounded-xl font-bold text-xs border border-blue-100 hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Navegar hacia el Domicilio GPS
                        </a>
                    </div>

                    {{-- RESUMEN DE COBRO Y PAGO --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Total a Cobrar</span>
                            <span class="text-lg font-black text-red-500 font-mono">${{ number_format($currentOrder->total, 2) }}</span>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Confirmación de Cobro / Estado</label>
                            <select wire:model="paymentOutcome" class="w-full text-xs rounded-xl border-gray-200 focus:border-gray-900 focus:ring-0 p-2.5 bg-gray-50 font-bold text-gray-800">
                                <option value="paid_correctly">✓ Pagado y entregado correctamente</option>
                                <option value="client_refused_payment">✕ Cliente no quiso pagar</option>
                                <option value="client_refused_delivery">✕ Cliente rechaza el paquete</option>
                            </select>
                        </div>

                        @if($paymentOutcome !== 'paid_correctly')
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-wider text-red-500">Motivo del Problema / Observación</label>
                                <textarea wire:model="incidentNotes" rows="2" class="w-full text-xs rounded-xl border-red-200 focus:border-red-500 focus:ring-0 p-2.5 bg-red-50/50" placeholder="Explica brevemente qué sucedió..."></textarea>
                            </div>
                        @endif
                    </div>

                    {{-- SLIDER IPHONE COMPLETAR ENTREGA --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-100/80 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 text-center">Desliza para Finalizar Pedido</p>

                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            init() { this.maxSwipe = $refs.track.clientWidth - $refs.thumb.clientWidth - 8; },
                            touchStart(e) { this.startX = e.touches[0].clientX; },
                            touchMove(e) { 
                                let diff = e.touches[0].clientX - this.startX;
                                if(diff > 0 && diff <= this.maxSwipe) this.currentX = diff;
                            },
                            touchEnd() {
                                if(this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    $wire.completeDelivery();
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" class="relative select-none">
                            <div x-ref="track" class="h-14 bg-gray-900 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden">
                                <span class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none">Finalizar Entrega &gt;&gt;</span>
                                <div x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @touchstart="touchStart" 
                                     @touchmove="touchMove" 
                                     @touchend="touchEnd"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75">
                                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    @endif
</div>