@assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endassets

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
                    <span class="h-1 w-1 rounded-full bg-red-500 shrink-0"></span>
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

    {{-- SECCIÓN 2: FORMULARIO INTEGRADO (REFERENCIAS E INSTRUCCIONES) --}}
    <div class="p-4 border-b border-gray-100 space-y-4">
        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-1">Referencias del lugar *</label>
            <input type="text" wire:model.blur="reference" placeholder="Ej. Portón negro, frente al parque..."
                class="w-full border-0 border-b-2 border-gray-200 px-0 py-2.5 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-red-500 focus:outline-none rounded-none transition-colors">
            @error('reference') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-1">Indicaciones especiales de entrega</label>
            <input type="text" wire:model.blur="specialInstructions" placeholder="Ej. Tocar timbre B o dejar en caseta..."
                class="w-full border-0 border-b-2 border-gray-200 px-0 py-2.5 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:ring-0 focus:border-red-500 focus:outline-none rounded-none transition-colors">
        </div>
    </div>

    {{-- SECCIÓN 3: SELECTOR DE MÉTODO DE PAGO NATIVO TÁCTIL --}}
    <div class="p-4 border-b border-gray-100">
        <label class="block text-xs font-black uppercase tracking-wider text-gray-400 mb-3">Método de Pago</label>
        
        <div class="flex border border-gray-200 rounded-xl overflow-hidden p-0.5 bg-gray-50">
            <button type="button" wire:click="setPaymentMethod('card')"
                class="flex-1 py-3 text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2 
                {{ $paymentMethod === 'card' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' }}">
                <i class="fas fa-credit-card text-xs"></i> Tarjeta
            </button>
            <button type="button" wire:click="setPaymentMethod('cash')"
                class="flex-1 py-3 text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2 
                {{ $paymentMethod === 'cash' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' }}">
                <i class="fas fa-money-bill-wave text-xs"></i> Efectivo
            </button>
        </div>

        {{-- AVISO DE TERMINAL FÍSICA PARA COBRO CON TARJETA --}}
        @if($paymentMethod === 'card')
            <div class="mt-3 flex items-start gap-3 bg-blue-50/70 rounded-xl p-3 border border-blue-100/50 animate-fadeIn">
                <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-blue-500 text-white text-[10px]">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="space-y-0.5">
                    <p class="text-xs font-black text-blue-950">Pago al entregar</p>
                    <p class="text-xs text-blue-800/90 font-medium leading-relaxed">
                        El repartidor llevará una <strong class="font-black text-blue-950">terminal punto de venta física</strong> para que deslices o acerques tu tarjeta al recibir tu pedido.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- SECCIÓN 4: DESGLOSE COMPLETO ESTILO RECIBO CONTINUO --}}
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

    {{-- FOOTER SEGURO ELEVADO EN EL AIRE (A VALOR DE BOTTOM-24 PARA SALVAR EL MENÚ MOBILE) --}}
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
                <i class="fas fa-shield-alt text-[8px]"></i> Pago 100% seguro y cifrado.
            </p>
        </div>
    </div>

</div>