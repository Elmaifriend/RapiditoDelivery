<div class="flex flex-col pb-8">

    {{-- CABECERA / IDENTIFICADOR DEL REPARTIDOR --}}
    <div class="bg-white text-gray-800 px-4 py-3.5 text-center text-xs font-semibold flex items-center justify-between shadow-xs border-b border-gray-100">
        <span class="truncate">Panel Repartidor: <strong class="text-red-500">{{ $driver->name ?? 'ID #'.$driver->id }}</strong></span>
        <span class="inline-flex items-center gap-1 bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-[10px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Activo
        </span>
    </div>

    {{-- SI NO HAY PEDIDO ASIGNADO --}}
    @if(!$currentOrder)
        <div wire:key="no-order-assigned" class="bg-white p-8 rounded-3xl border border-gray-100 text-center space-y-4 my-auto max-w-md mx-4 md:mx-auto w-auto shadow-xs">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 text-gray-400 text-3xl">
                <i class="bxf bx-package text-4xl text-gray-300"></i>
            </div>
            
            <div class="space-y-1">
                <h1 class="text-xl font-bold tracking-tight text-gray-900">No hay pedidos en este momento</h1>
                <p class="text-xs text-gray-500 max-w-xs mx-auto leading-relaxed font-semibold">
                    Estás en línea y disponible. Tan pronto como un restaurante marque una orden lista, se te asignará automáticamente.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 bg-green-50 px-3.5 py-1.5 rounded-full border border-green-100">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-green-700">Esperando tareas...</span>
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
        <div wire:key="header-order-{{ $currentOrder->id }}-{{ $currentOrder->delivery_status->value }}" class="bg-white px-4 pt-10 pb-6 border-b border-gray-100 text-center space-y-3">
            <div class="inline-block bg-gray-50 border border-gray-100 px-4 py-1.5 rounded-full">
                <p class="text-xs font-bold text-gray-700 tracking-wider uppercase">
                    Orden #{{ str_pad($currentOrder->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            @if($isHeadingToRestaurant)
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Ve a recoger este pedido</h1>
                <p class="text-xs font-bold text-amber-600 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    Paso 1: Dirígete al Restaurante
                </p>
            @else
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Entrega este pedido</h1>
                <p class="text-xs font-bold text-blue-600 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                    Paso 2: En trayecto al cliente
                </p>
            @endif
        </div>

        {{-- CONTENEDOR PRINCIPAL CON ALPINE Y WIRE:KEY UNIFICADO --}}
        <div wire:key="order-container-{{ $currentOrder->id }}-{{ $currentOrder->delivery_status->value }}" 
             class="max-w-md mx-auto w-full space-y-4 p-4" 
             x-data="{ step: '{{ $isHeadingToRestaurant ? 'pickup' : 'deliver' }}' }"
             x-effect="step = '{{ $isHeadingToRestaurant ? 'pickup' : 'deliver' }}'">

            {{-- ======================================================== --}}
            {{-- ETAPA 1: IR AL RESTAURANTE --}}
            {{-- ======================================================== --}}
            @if($isHeadingToRestaurant)
                
                {{-- VISTA DE DIRECCIÓN DEL RESTAURANTE + MAPA --}}
                <div x-show="step === 'pickup'" class="space-y-4">
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                            <i class="bxf bx-store-alt text-base text-gray-400"></i>
                            Restaurante de Origen
                        </h3>
                        <div>
                            <p class="text-sm font-extrabold text-gray-800 leading-snug">{{ $restaurant->name ?? 'Restaurante' }}</p>
                            <p class="text-xs font-semibold text-gray-500 mt-1">{{ $restaurant->address ?? 'Sin dirección' }}</p>
                        </div>

                        {{-- MAPA DEL RESTAURANTE --}}
                        <div class="relative w-full h-40 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 shadow-xs">
                            <div 
                                x-init="
                                    $nextTick(() => {
                                        const map = L.map($el, {
                                            zoomControl: false,
                                            attributionControl: false,
                                            dragging: false,
                                            scrollWheelZoom: false,
                                            touchZoom: false,
                                            doubleClickZoom: false
                                        }).setView([{{ $restaurant->lat ?? 32.5149 }}, {{ $restaurant->lng ?? -117.0382 }}], 14);

                                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                                            maxZoom: 20
                                        }).addTo(map);
                                    })
                                "
                                class="absolute inset-0 z-0 h-full w-full outline-none"
                                wire:ignore
                            ></div>

                            <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 flex -translate-x-1/2 -translate-y-[90%] flex-col items-center">
                                <div class="relative">
                                    <svg class="drop-shadow-2xl" width="50" height="60" viewBox="0 0 50 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M25 0C11.1929 0 0 11.1929 0 25C0 39.5 25 60 25 60C25 60 50 39.5 50 25C50 11.1929 38.8071 0 25 0Z" fill="#e7000b" />
                                        <circle cx="25" cy="24" r="18" fill="white" />
                                    </svg>
                                    <div class="absolute left-[13px] top-[12px]">
                                        <i class="bxf bx-carrot text-2xl text-red-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- LINK A GOOGLE MAPS GPS --}}
                        <a href="{{ $restaurantMapsUrl }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-red-50 text-red-500 rounded-2xl font-bold text-xs border border-red-100 hover:bg-red-100 transition-colors cursor-pointer">
                            <i class="bxf bx-navigation text-base"></i>
                            Abrir en Google Maps GPS
                        </a>
                    </div>

                    {{-- SLIDER PARA LLEGAR AL RESTAURANTE --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 text-center">Desliza cuando llegues al restaurante</p>
                        
                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            completed: false,
                            isDragging: false,
                            dragStart(e) {
                                if (this.completed) return;
                                this.maxSwipe = this.$refs.track.clientWidth - this.$refs.thumb.clientWidth - 8; 
                                this.isDragging = true;
                                this.startX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                            },
                            dragMove(e) {
                                if (!this.isDragging || this.completed) return;
                                const x = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                                let diff = x - this.startX;
                                if (diff < 0) diff = 0;
                                if (diff > this.maxSwipe) diff = this.maxSwipe;
                                this.currentX = diff;
                            },
                            dragEnd() {
                                if (!this.isDragging || this.completed) return;
                                this.isDragging = false;
                                if (this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    this.completed = true;
                                    setTimeout(() => { step = 'checklist'; }, 200);
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" 
                        x-on:reset-pickup-slider.window="currentX = 0; completed = false; isDragging = false;"
                        class="relative select-none">
                            <div 
                                x-ref="track" 
                                @mousemove="dragMove"
                                @mouseup="dragEnd"
                                @mouseleave="dragEnd"
                                @touchmove.prevent="dragMove"
                                @touchend="dragEnd"
                                class="h-14 bg-red-500 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden shadow-inner"
                            >
                                <span 
                                    :style="`opacity: ${maxSwipe > 0 ? 1 - (currentX / maxSwipe) : 1}; filter: blur(${maxSwipe > 0 ? (currentX / maxSwipe) * 4 : 0}px)`"
                                    class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none transition-all duration-75"
                                >
                                    Llegué al Restaurante &gt;&gt;
                                </span>
                                <div 
                                     x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @mousedown="dragStart"
                                     @touchstart="dragStart"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75"
                                >
                                    <i class="bxf bx-chevron-right text-xl text-red-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHECKLIST Y CONFIRMACIÓN DE PRODUCTOS EN RESTAURANTE --}}
                <div x-show="step === 'checklist'" x-cloak x-data="{ checkedItems: {} }" class="space-y-4">
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="bxf bx-list-check text-base text-gray-400"></i>
                                Verificación de Productos
                            </h3>
                            <button @click="step = 'pickup'; $dispatch('reset-pickup-slider')" class="text-[10px] font-bold text-red-500 hover:text-red-600 bg-red-50 px-2 py-1 rounded-lg cursor-pointer">
                                Volver
                            </button>
                        </div>

                        <div class="divide-y divide-gray-100 border-t border-b border-gray-100 py-1">
                            @foreach($currentOrder->items as $index => $item)
                                <div 
                                    @click="checkedItems[{{ $index }}] = !checkedItems[{{ $index }}]"
                                    class="py-2.5 flex items-center justify-between cursor-pointer select-none active:bg-gray-50 transition-all px-2 -mx-2 rounded-xl"
                                >
                                    <span class="text-xs font-semibold text-gray-800 flex items-center gap-2">
                                        <i 
                                            class="bxf text-lg transition-all"
                                            :class="checkedItems[{{ $index }}] ? 'bx-check-square text-emerald-600' : 'bx-checkbox text-gray-400'"
                                        ></i>
                                        <span>
                                            <strong class="font-bold text-gray-900 bg-gray-200/50 px-1.5 py-0.5 rounded mr-1.5">{{ $item->quantity }}x</strong> 
                                            {{ $item->product_name_snapshot }}
                                        </span>
                                    </span>
                                    <span 
                                        class="text-[10px] font-bold transition-all px-2.5 py-1 rounded-lg"
                                        :class="checkedItems[{{ $index }}] ? 'text-green-600 bg-green-50' : 'text-gray-400 bg-gray-100'"
                                        x-text="checkedItems[{{ $index }}] ? 'Listo' : 'Pendiente'"
                                    ></span>
                                </div>
                            @endforeach
                        </div>

                        {{-- REPORTES DEL CONDUCTOR --}}
                        <div class="space-y-2 pt-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">¿Todo está en orden?</label>
                            <div class="flex gap-2">
                                <button type="button" @click="$wire.set('allItemsCorrect', true)" :class="$wire.allItemsCorrect ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-50 border border-gray-100 text-gray-600'" class="flex-1 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer">
                                    ✓ Todo Correcto
                                </button>
                                <button type="button" @click="$wire.set('allItemsCorrect', false)" :class="!$wire.allItemsCorrect ? 'bg-red-500 text-white shadow-xs' : 'bg-gray-50 border border-gray-100 text-gray-600'" class="flex-1 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer">
                                    ✕ Falta algo / Detalle
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Notas / Comentarios adicionales</label>
                            <textarea wire:model="driverNotes" rows="4" class="w-full text-xs rounded-2xl border border-gray-100 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10 p-3 bg-gray-50 text-gray-800 placeholder-gray-400 transition-all font-semibold" placeholder="Escribe aquí si hubo algún detalle con el empaque o producto..."></textarea>
                        </div>
                    </div>

                    {{-- SLIDER CONFIRMACIÓN DE RECOGIDA CON ACCIÓN DE LIVEWIRE --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 text-center">Desliza para Iniciar Viaje al Cliente</p>

                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            completed: false,
                            isDragging: false,
                            dragStart(e) {
                                if (this.completed) return;
                                this.maxSwipe = this.$refs.track.clientWidth - this.$refs.thumb.clientWidth - 8; 
                                this.isDragging = true;
                                this.startX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                            },
                            dragMove(e) {
                                if (!this.isDragging || this.completed) return;
                                const x = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                                let diff = x - this.startX;
                                if (diff < 0) diff = 0;
                                if (diff > this.maxSwipe) diff = this.maxSwipe;
                                this.currentX = diff;
                            },
                            dragEnd() {
                                if (!this.isDragging || this.completed) return;
                                this.isDragging = false;
                                if (this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    this.completed = true;
                                    $wire.markAsPickedUp();
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" class="relative select-none">
                            <div 
                                x-ref="track" 
                                @mousemove="dragMove"
                                @mouseup="dragEnd"
                                @mouseleave="dragEnd"
                                @touchmove.prevent="dragMove"
                                @touchend="dragEnd"
                                class="h-14 bg-emerald-600 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden shadow-inner"
                            >
                                <span 
                                    :style="`opacity: ${maxSwipe > 0 ? 1 - (currentX / maxSwipe) : 1}; filter: blur(${maxSwipe > 0 ? (currentX / maxSwipe) * 4 : 0}px)`"
                                    class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none transition-all duration-75"
                                    x-text="completed ? 'Procesando...' : 'Confirmar Recolección >>'"
                                ></span>
                                <div 
                                     x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @mousedown="dragStart"
                                     @touchstart="dragStart"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75"
                                >
                                    <i class="bxf bx-chevron-right text-xl text-emerald-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            {{-- ======================================================== --}}
            {{-- ETAPA 2: IR AL CLIENTE (EN DOS PASOS) --}}
            {{-- ======================================================== --}}
            @else
                
                {{-- PASO 2A: VISTA DE DIRECCIÓN Y NAVEGACIÓN HASTA LLEGAR AL DOMICILIO --}}
                <div x-show="step === 'deliver'" class="space-y-4">
                    {{-- TARJETA DEL CLIENTE --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5 mb-1.5">
                                    <i class="bxf bx-user text-base text-gray-400"></i>
                                    Punto de Entrega
                                </h3>
                                <p class="text-sm font-extrabold text-gray-800 leading-snug">{{ $currentOrder->customer_name ?? 'Cliente' }}</p>
                                <p class="text-xs font-semibold text-gray-500 mt-1 leading-snug">{{ $dropoff?->formatted_address ?? $dropoff?->address_line ?? 'Sin dirección disponible' }}</p>
                            </div>
                            @if($currentOrder->customer_phone)
                                <a href="tel:{{ $currentOrder->customer_phone }}" class="shrink-0 flex items-center gap-1 text-xs font-bold text-red-500 bg-red-50 px-2.5 py-1.5 rounded-xl hover:bg-red-100 transition-colors active:scale-95">
                                    <i class="bxf bx-phone text-sm"></i>
                                    Llamar
                                </a>
                            @endif
                        </div>

                        @if($dropoff?->reference)
                            <div class="bg-amber-50 rounded-2xl p-3 border border-amber-100 text-xs text-amber-800 font-semibold leading-relaxed">
                                <span class="font-bold block mb-0.5">Referencias de entrega:</span>
                                {{ $dropoff->reference }}
                            </div>
                        @endif

                        @if($dropoff?->delivery_instructions)
                            <div class="bg-blue-50 rounded-2xl p-3 border border-blue-100 text-xs text-blue-800 font-semibold leading-relaxed">
                                <span class="font-bold block mb-0.5">Indicaciones para el repartidor:</span>
                                {{ $dropoff->delivery_instructions }}
                            </div>
                        @endif

                        {{-- MAPA DEL CLIENTE --}}
                        <div class="relative w-full h-40 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 shadow-xs">
                            <div 
                                x-init="
                                    $nextTick(() => {
                                        const map = L.map($el, {
                                            zoomControl: false,
                                            attributionControl: false,
                                            dragging: false,
                                            scrollWheelZoom: false,
                                            touchZoom: false,
                                            doubleClickZoom: false
                                        }).setView([{{ $dropoff->lat ?? 32.5149 }}, {{ $dropoff->lng ?? -117.0382 }}], 14);

                                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                                            maxZoom: 20
                                        }).addTo(map);
                                    })
                                "
                                class="absolute inset-0 z-0 h-full w-full outline-none"
                                wire:ignore
                            ></div>

                            <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 flex -translate-x-1/2 -translate-y-[90%] flex-col items-center">
                                <div class="relative">
                                    <svg class="drop-shadow-2xl" width="50" height="60" viewBox="0 0 50 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M25 0C11.1929 0 0 11.1929 0 25C0 39.5 25 60 25 60C25 60 50 39.5 50 25C50 11.1929 38.8071 0 25 0Z" fill="#e7000b" />
                                        <circle cx="25" cy="24" r="18" fill="white" />
                                    </svg>
                                    <div class="absolute left-[13px] top-[12px]">
                                        <i class="bxf bx-carrot text-2xl text-red-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- LINK A GOOGLE MAPS GPS CLIENTE --}}
                        <a href="{{ $dropoffMapsUrl }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-red-50 text-red-500 rounded-2xl font-bold text-xs border border-red-100 hover:bg-red-100 transition-colors cursor-pointer">
                            <i class="bxf bx-navigation text-base"></i>
                            Abrir en Google Maps GPS
                        </a>
                    </div>

                    {{-- RESUMEN DE LA ORDEN --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="bxf bx-receipt text-base text-gray-400"></i>
                                Resumen del Pedido
                            </h3>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
                                {{ $currentOrder->payment_method->value === 'card' ? 'Tarjeta' : 'Efectivo' }}
                            </span>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tienda de origen</p>
                            <p class="text-sm font-extrabold text-gray-800 mt-1">{{ $restaurant->name ?? 'Restaurante' }}</p>
                        </div>

                        <div class="divide-y divide-gray-100 border-t border-b border-gray-100 py-1">
                            @foreach($currentOrder->items as $item)
                                <div class="py-2 flex items-center justify-between text-xs">
                                    <span class="text-gray-800 font-semibold">
                                        <strong class="font-bold text-gray-900 bg-gray-200/50 px-1.5 py-0.5 rounded mr-1.5">{{ $item->quantity }}x</strong> 
                                        {{ $item->product_name_snapshot }}
                                    </span>
                                    <span class="font-bold text-gray-700 font-mono">${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if($currentOrder->special_instructions)
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-3 text-xs text-gray-700 font-semibold">
                                <span class="font-bold text-gray-500 block mb-0.5">Indicaciones de cocina:</span>
                                {{ $currentOrder->special_instructions }}
                            </div>
                        @endif

                        <div class="space-y-2 pt-1">
                            <div class="flex justify-between text-xs font-semibold text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-mono text-gray-700">${{ number_format($currentOrder->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-semibold text-gray-500">
                                <span>Envío a domicilio</span>
                                <span class="font-mono text-gray-700">${{ number_format($currentOrder->delivery_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-bold text-gray-900 pt-3 border-t border-gray-100">
                                <span>Total a cobrar</span>
                                <span class="font-mono text-base text-red-600 font-bold">${{ number_format($currentOrder->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- SLIDER: LLEGUÉ AL DOMICILIO (CAMBIA VISTA EN FRONTEND) --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 text-center">Desliza cuando llegues con el cliente</p>

                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            completed: false,
                            isDragging: false,
                            dragStart(e) {
                                if (this.completed) return;
                                this.maxSwipe = this.$refs.track.clientWidth - this.$refs.thumb.clientWidth - 8; 
                                this.isDragging = true;
                                this.startX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                            },
                            dragMove(e) {
                                if (!this.isDragging || this.completed) return;
                                const x = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                                let diff = x - this.startX;
                                if (diff < 0) diff = 0;
                                if (diff > this.maxSwipe) diff = this.maxSwipe;
                                this.currentX = diff;
                            },
                            dragEnd() {
                                if (!this.isDragging || this.completed) return;
                                this.isDragging = false;
                                if (this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    this.completed = true;
                                    setTimeout(() => { step = 'arrived'; }, 200);
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" 
                        x-on:reset-arrived-slider.window="currentX = 0; completed = false; isDragging = false;"
                        class="relative select-none">
                            <div 
                                x-ref="track" 
                                @mousemove="dragMove"
                                @mouseup="dragEnd"
                                @mouseleave="dragEnd"
                                @touchmove.prevent="dragMove"
                                @touchend="dragEnd"
                                class="h-14 bg-red-500 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden shadow-inner"
                            >
                                <span 
                                    :style="`opacity: ${maxSwipe > 0 ? 1 - (currentX / maxSwipe) : 1}; filter: blur(${maxSwipe > 0 ? (currentX / maxSwipe) * 4 : 0}px)`"
                                    class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none transition-all duration-75"
                                >
                                    Llegué al Domicilio >>
                                </span>
                                <div 
                                     x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @mousedown="dragStart"
                                     @touchstart="dragStart"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75"
                                >
                                    <i class="bxf bx-chevron-right text-xl text-red-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PASO 2B: REGISTRO DE ESTADO DE PAGO, INCIDENCIAS Y FINALIZACIÓN --}}
                <div x-show="step === 'arrived'" x-cloak class="space-y-4">
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="bxf bx-wallet text-base text-gray-400"></i>
                                Confirmación de Pago y Entrega
                            </h3>
                            <button @click="step = 'deliver'; $dispatch('reset-arrived-slider')" class="text-[10px] font-bold text-red-500 hover:text-red-600 bg-red-50 px-2 py-1 rounded-lg cursor-pointer">
                                Volver al Mapa
                            </button>
                        </div>

                        {{-- Opciones de Pago / Resultado --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Resultado de la entrega</label>
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" 
                                        wire:click="$set('paymentOutcome', 'paid_correctly')"
                                        class="flex items-center justify-between p-3.5 rounded-2xl border text-xs font-bold transition-all cursor-pointer {{ $paymentOutcome === 'paid_correctly' ? 'border-emerald-500 bg-emerald-50 text-emerald-900' : 'border-gray-150 bg-gray-50 text-gray-600' }}">
                                    <span>✓ Pagado correctamente</span>
                                    @if($paymentOutcome === 'paid_correctly')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    @endif
                                </button>

                                <button type="button" 
                                        wire:click="$set('paymentOutcome', 'client_refused_payment')"
                                        class="flex items-center justify-between p-3.5 rounded-2xl border text-xs font-bold transition-all cursor-pointer {{ $paymentOutcome === 'client_refused_payment' ? 'border-amber-500 bg-amber-50 text-amber-900' : 'border-gray-150 bg-gray-50 text-gray-600' }}">
                                    <span>⚠️ Cliente rechaza pagar</span>
                                    @if($paymentOutcome === 'client_refused_payment')
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    @endif
                                </button>

                                <button type="button" 
                                        wire:click="$set('paymentOutcome', 'client_refused_delivery')"
                                        class="flex items-center justify-between p-3.5 rounded-2xl border text-xs font-bold transition-all cursor-pointer {{ $paymentOutcome === 'client_refused_delivery' ? 'border-red-500 bg-red-50 text-red-900' : 'border-gray-150 bg-gray-50 text-gray-600' }}">
                                    <span>✕ Cliente rechaza pedido / Devolución</span>
                                    @if($paymentOutcome === 'client_refused_delivery')
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    @endif
                                </button>
                            </div>
                        </div>

                        {{-- Campo opcional para notas si hay alguna incidencia --}}
                        @if($paymentOutcome !== 'paid_correctly')
                            <div class="space-y-1.5 pt-1" x-transition>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Detalle de la incidencia</label>
                                <textarea wire:model="incidentNotes" 
                                        rows="4" 
                                        class="w-full text-xs rounded-2xl border-amber-200 focus:border-amber-500 focus:ring-0 p-3 bg-amber-50/50 text-amber-900 placeholder-amber-400 font-semibold" 
                                        placeholder="Describe por qué el cliente no pagó o rechazó el pedido..."></textarea>
                            </div>
                        @endif
                    </div>

                    {{-- SLIDER FINALIZAR ENTREGA --}}
                    <div class="flex flex-col gap-4 shadow-xs rounded-2xl border border-gray-100/50 bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 text-center">Desliza para Finalizar Entrega</p>

                        <div x-data="{ 
                            startX: 0, 
                            currentX: 0, 
                            maxSwipe: 0, 
                            completed: false,
                            isDragging: false,
                            dragStart(e) {
                                if (this.completed) return;
                                this.maxSwipe = this.$refs.track.clientWidth - this.$refs.thumb.clientWidth - 8; 
                                this.isDragging = true;
                                this.startX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                            },
                            dragMove(e) {
                                if (!this.isDragging || this.completed) return;
                                const x = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                                let diff = x - this.startX;
                                if (diff < 0) diff = 0;
                                if (diff > this.maxSwipe) diff = this.maxSwipe;
                                this.currentX = diff;
                            },
                            dragEnd() {
                                if (!this.isDragging || this.completed) return;
                                this.isDragging = false;
                                if (this.currentX >= this.maxSwipe * 0.85) {
                                    this.currentX = this.maxSwipe;
                                    this.completed = true;
                                    $wire.completeDelivery();
                                } else {
                                    this.currentX = 0;
                                }
                            }
                        }" class="relative select-none">
                            <div 
                                x-ref="track" 
                                @mousemove="dragMove"
                                @mouseup="dragEnd"
                                @mouseleave="dragEnd"
                                @touchmove.prevent="dragMove"
                                @touchend="dragEnd"
                                class="h-14 bg-emerald-600 rounded-2xl p-1 flex items-center justify-center relative overflow-hidden shadow-inner"
                            >
                                <span 
                                    :style="`opacity: ${maxSwipe > 0 ? 1 - (currentX / maxSwipe) : 1}; filter: blur(${maxSwipe > 0 ? (currentX / maxSwipe) * 4 : 0}px)`"
                                    class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none transition-all duration-75"
                                    x-text="completed ? 'Finalizando...' : 'Finalizar Entrega >>'"
                                ></span>
                                <div 
                                     x-ref="thumb" 
                                     :style="`transform: translateX(${currentX}px)`"
                                     @mousedown="dragStart"
                                     @touchstart="dragStart"
                                     class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75"
                                >
                                    <i class="bxf bx-chevron-right text-xl text-emerald-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endif

</div>