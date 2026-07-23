<div class="flex flex-col min-h-screen bg-white pb-36"
     x-data="{
        map: null,
        lat: {{ $this->currentAddress?->lat ?? 32.5149 }},
        lng: {{ $this->currentAddress?->lng ?? -117.0382 }},

        init() {
            this.$nextTick(() => { this.initMap(); });
        },

        initMap() {
            if (this.map) return;

            this.map = L.map('delivery-map', {
                zoomControl: false, 
                dragging: false, 
                scrollWheelZoom: false,
                touchZoom: false,
                doubleClickZoom: false
            }).setView([this.lat, this.lng], 16);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '', subdomains: 'abcd', maxZoom: 20
            }).addTo(this.map);

            L.marker([this.lat, this.lng]).addTo(this.map);
        }
     }"
>

    {{-- HEADER SUPERIOR DE LA VENTANA --}}
    <div class="px-4 pt-5 pb-3 border-b border-gray-100 sticky top-0 bg-white/95 backdrop-blur-md z-30">
        <h2 class="text-xl font-black tracking-tight text-gray-900">Finalizar Pedido</h2>
    </div>

    {{-- ERRORES DE VALIDACIÓN --}}
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 text-xs font-bold border-b border-red-100 space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500 shrink-0"></span>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    {{-- SECCIÓN 1: MINI MAPA TOTALMENTE EMBEBIDO --}}
    <div class="relative w-full h-44 bg-gray-100 border-b border-gray-100">
        <div id="delivery-map" wire:ignore class="absolute inset-0 z-0 h-full w-full outline-none"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none z-10"></div>
    </div>

    {{-- CONTENEDOR DE LA DIRECCIÓN DE ENTREGA ACTUAL --}}
    <div class="p-4 border-b border-gray-100 space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-wider text-red-500 bg-red-50 px-2 py-0.5 rounded-md">
                Entregar en {{ $this->currentAddress?->label ?? 'Pedido' }}
            </span>
            <a href="/location?mode=checkout" wire:navigate class="text-xs font-bold text-gray-500 flex items-center gap-1 active:opacity-60 py-1">
                <i class="fas fa-edit text-[10px]"></i> Cambiar dirección
            </a>
        </div>
        <p class="text-base font-bold text-gray-900 leading-snug">
            {{ $this->currentAddress?->formatted_address ?? $this->currentAddress?->address_line ?? 'Sin dirección seleccionada' }}
        </p>
    </div>

    {{-- SECCIÓN: DATOS DE CONTACTO DE ENTREGA CON SELECTOR DE LADA --}}
    <div class="p-4 border-b border-gray-100 space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-gray-400">Datos de contacto</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tu Nombre Completo *</label>
                <input type="text" wire:model.blur="customerName" placeholder="Ej. Juan Pérez"
                    class="w-full border-0 border-b-2 border-gray-200 px-0 py-2 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-red-500 focus:outline-none rounded-none transition-colors">
                @error('customerName') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">WhatsApp / Teléfono *</label>
                <div class="flex items-end gap-2 border-b-2 border-gray-200 focus-within:border-red-500 transition-colors">
                    {{-- SELECTOR DE LADA --}}
                    <select wire:model.live="countryCode" 
                        class="bg-transparent border-0 px-0 py-2 text-sm font-bold text-gray-800 focus:ring-0 focus:outline-none cursor-pointer">
                        @foreach (\App\Enums\CountryCode::cases() as $country)
                            <option value="{{ $country->name }}">
                                {{ $country->dialCode() }} ({{ $country->name }})
                            </option>
                        @endforeach
                    </select>

                    {{-- CAMPO DE TELÉFONO --}}
                    <input type="tel" wire:model.blur="customerPhone" placeholder="6641234567"
                        class="w-full border-0 px-0 py-2 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none rounded-none">
                </div>
                @error('customerPhone') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: INSTRUCCIONES DE ENTREGA (PARA EL REPARTIDOR) --}}
    <div class="p-4 border-b border-gray-100 space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-gray-400">Datos para la entrega</h3>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-1">Referencias del lugar *</label>
            <input type="text" wire:model.blur="reference" placeholder="Ej. Portón negro, fachada blanca..."
                class="w-full border-0 border-b-2 border-gray-200 px-0 py-2.5 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-red-500 focus:outline-none rounded-none transition-colors">
            @error('reference') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-1">Indicaciones para el repartidor</label>
            <input type="text" wire:model.blur="deliveryInstructions" placeholder="Ej. Tocar timbre B, dejar en caseta o llamar al llegar..."
                class="w-full border-0 border-b-2 border-gray-200 px-0 py-2.5 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-red-500 focus:outline-none rounded-none transition-colors">
            @error('deliveryInstructions') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- SECCIÓN 3: INSTRUCCIONES PARA LA COCINA / RESTAURANTE --}}
    <div class="p-4 border-b border-gray-100 space-y-4 bg-orange-50/30">
        <h3 class="text-xs font-black uppercase tracking-wider text-orange-600 flex items-center gap-1.5">
            <i class="fas fa-utensils text-[10px]"></i> Indicaciones para la cocina
        </h3>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-1">¿Algún detalle con tu platillo?</label>
            <input type="text" wire:model.blur="specialInstructions" placeholder="Ej. Sin cebolla, poca sal, aderezo aparte..."
                class="w-full border-0 border-b-2 border-gray-200 px-0 py-2.5 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-orange-500 focus:outline-none rounded-none transition-colors bg-transparent">
            @error('specialInstructions') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- SECCIÓN 4: SELECTOR DE MÉTODO DE PAGO --}}
    <div class="p-4 border-b border-gray-100 space-y-3">
        <label class="block text-xs font-black uppercase tracking-wider text-gray-400">¿Cómo deseas pagar?</label>
        
        <div class="grid grid-cols-2 gap-2 bg-gray-100 p-1 rounded-2xl">
            {{-- OPCIÓN 1: EFECTIVO --}}
            <button type="button" wire:click="setPaymentMethod('cash')"
                class="flex items-center justify-center gap-2 py-3.5 px-3 rounded-xl font-extrabold text-sm transition-all duration-200
                {{ $paymentMethod === 'cash' ? 'bg-white text-emerald-700 shadow-md scale-[1.01]' : 'text-gray-500 hover:text-gray-800' }}">
                <i class="fas fa-money-bill-wave text-base {{ $paymentMethod === 'cash' ? 'text-emerald-500' : 'text-gray-400' }}"></i>
                <span>Efectivo</span>
            </button>

            {{-- OPCIÓN 2: TARJETA EN TERMINAL --}}
            <button type="button" wire:click="setPaymentMethod('card')"
                class="flex items-center justify-center gap-2 py-3.5 px-3 rounded-xl font-extrabold text-sm transition-all duration-200
                {{ $paymentMethod === 'card' ? 'bg-white text-blue-700 shadow-md scale-[1.01]' : 'text-gray-500 hover:text-gray-800' }}">
                <i class="fas fa-credit-card text-base {{ $paymentMethod === 'card' ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Tarjeta</span>
            </button>
        </div>

        {{-- INFORMATIVO: EFECTIVO --}}
        @if($paymentMethod === 'cash')
            <div class="flex items-start gap-3.5 bg-emerald-50/80 border border-emerald-200/60 rounded-2xl p-4 transition-all">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm">
                    <i class="fas fa-hand-holding-usd text-lg"></i>
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-black text-emerald-950">Pago en efectivo al entregar</h4>
                    <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                        Le entregarás el importe exacto o cambio en efectivo al repartidor al momento de recibir tu pedido.
                    </p>
                </div>
            </div>
        @endif

        {{-- INFORMATIVO: TARJETA EN TERMINAL --}}
        @if($paymentMethod === 'card')
            <div class="flex items-start gap-3.5 bg-blue-50/90 border border-blue-200/70 rounded-2xl p-4 transition-all">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                    <i class="fas fa-mobile-alt text-lg"></i>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-black text-blue-950">Pago con Terminal Física</h4>
                        <span class="bg-blue-200 text-blue-900 text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Al recibir</span>
                    </div>
                    <p class="text-xs text-blue-900 font-medium leading-relaxed">
                        El repartidor llevará una <strong class="font-bold text-blue-950">terminal bancaria inalámbrica</strong>. Podrás pagar con chip, banda o pago sin contacto (*Contactless / Apple Pay*) al recibir tu compra.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- SECCIÓN 5: DESGLOSE COMPLETO ESTILO RECIBO CONTINUO --}}
    @if($this->cart)
        <div class="p-4 space-y-2.5 bg-gray-50/50">
            <div class="flex justify-between text-xs font-semibold text-gray-500">
                <span>Productos ({{ $this->cart->items->sum('quantity') }})</span>
                <span class="font-mono">${{ number_format($this->cart->subtotal, 2) }}</span>
            </div>
            
            <div class="flex justify-between text-xs font-semibold {{ is_null($this->deliveryFee) ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                <span>Envío a domicilio</span>
                <span class="font-mono">
                    @if(is_null($this->deliveryFee))
                        Fuera de cobertura
                    @else
                        ${{ number_format($this->deliveryFee, 2) }}
                    @endif
                </span>
            </div>

            <div class="flex justify-between text-base font-black text-gray-900 pt-3 border-t border-gray-200/60">
                <span>Total</span>
                <span class="font-mono text-lg">${{ number_format($this->totalAmount, 2) }}</span>
            </div>
        </div>
    @endif

    {{-- FOOTER SEGURO ELEVADO EN EL AIRE --}}
    <div class="fixed bottom-24 left-0 right-0 z-40 px-4 pointer-events-none">
        <div class="max-w-md mx-auto pointer-events-auto">
            <button wire:click="confirmPayment" wire:loading.attr="disabled"
                @if(is_null($this->deliveryFee)) disabled @endif
                class="flex w-full items-center justify-between rounded-2xl bg-red-500 p-4 font-bold text-white transition-all active:scale-[0.98] disabled:opacity-40 disabled:bg-gray-400 disabled:pointer-events-none shadow-2xl shadow-gray-900/20"
            >
                <div class="flex items-center gap-2 text-sm tracking-wide">
                    <i class="fas fa-lock text-xs" wire:loading.remove wire:target="confirmPayment"></i>
                    <i class="fas fa-spinner fa-spin text-xs" wire:loading wire:target="confirmPayment"></i>
                    <span wire:loading.remove wire:target="confirmPayment">Hacer Pedido</span>
                    <span wire:loading wire:target="confirmPayment">Procesando...</span>
                </div>
                
                <div class="flex items-center gap-1 text-sm font-black font-mono">
                    <span>${{ number_format($this->totalAmount, 2) }}</span>
                    <i class="fas fa-chevron-right text-xs opacity-80"></i>
                </div>
            </button>
            
            <p class="text-center text-[9px] text-gray-500 mt-1.5 drop-shadow-sm font-semibold">
                <i class="fas fa-shield-alt text-[8px]"></i> Pago 100% seguro y garantizado.
            </p>
        </div>
    </div>

</div>