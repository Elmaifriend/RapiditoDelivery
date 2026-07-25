<div
    class="pb-18 flex flex-col gap-4 px-4 pt-4"
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
                attributionControl: false,
                dragging: false,
                scrollWheelZoom: false,
                touchZoom: false,
                doubleClickZoom: false
            }).setView([this.lat, this.lng], 14);
    
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20
            }).addTo(this.map);
        }
    }"
>
    <div class="flex items-center gap-4">
        <a
            class="shadow-xs flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-100 bg-white text-gray-800 transition-all active:scale-95"
            href="/cart"
            wire:navigate
        >
            <i class="bxf bx-chevron-left text-2xl"></i>
        </a>
        <h2 class="text-lg font-bold tracking-tight text-gray-900">Finalizar Pedido</h2>
    </div>

    <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-gray-100/50 bg-white p-4">
        <div class="relative h-40 w-full overflow-hidden rounded-2xl border border-gray-100/50 bg-gray-100">
            <div
                class="absolute inset-0 z-0 h-full w-full outline-none"
                id="delivery-map"
                wire:ignore
            ></div>

            <div
                class="pointer-events-none absolute left-1/2 top-1/2 z-10 flex -translate-x-1/2 -translate-y-[90%] flex-col items-center">
                <div class="relative">
                    <svg
                        class="drop-shadow-2xl"
                        width="50"
                        height="60"
                        viewBox="0 0 50 60"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M25 0C11.1929 0 0 11.1929 0 25C0 39.5 25 60 25 60C25 60 50 39.5 50 25C50 11.1929 38.8071 0 25 0Z"
                            fill="#e7000b"
                        />
                        <circle
                            cx="25"
                            cy="24"
                            r="18"
                            fill="white"
                        />
                    </svg>
                    <div class="absolute left-[13px] top-[12px]">
                        <i class="bxf bx-carrot text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 px-2">
            <div class="space-y-1.5">
                <p class="mt-1 text-sm font-extrabold leading-snug text-gray-800">
                    {{ $this->currentAddress?->formatted_address ?? ($this->currentAddress?->address_line ?? 'Sin dirección seleccionada') }}
                </p>
            </div>
            <a
                class="flex shrink-0 items-center gap-1 rounded-xl bg-red-50 px-3 py-2 text-xs font-extrabold text-red-500 transition-all hover:text-red-600 active:scale-95"
                href="/location?mode=checkout"
                wire:navigate
            >
                Editar
            </a>
        </div>
    </div>

    <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-gray-100/50 bg-white p-4">
        <h3 class="text-sm font-bold text-gray-900">Datos de contacto</h3>

        <div class="grid grid-cols-1 gap-4">
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Tu Nombre Completo
                    *</label>
                <input
                    class="w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                    type="text"
                    wire:model.blur="customerName"
                    placeholder="Ej. Juan Pérez"
                >
                @error('customerName')
                    <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">WhatsApp / Teléfono
                    *</label>
                <div
                    class="flex items-center gap-2 rounded-2xl border border-gray-100 bg-gray-50 px-3 py-1 transition-all duration-200 focus-within:border-red-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-red-500/10">
                    <select
                        class="cursor-pointer border-0 bg-transparent px-1 py-2 text-sm font-extrabold text-gray-800 focus:outline-none focus:ring-0"
                        wire:model.live="countryCode"
                    >
                        @foreach (\App\Enums\CountryCode::cases() as $country)
                            <option value="{{ $country->name }}">
                                {{ $country->dialCode() }}
                            </option>
                        @endforeach
                    </select>

                    <span class="h-5 w-px bg-gray-200"></span>

                    <input
                        class="w-full border-0 bg-transparent px-1 py-2 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0"
                        type="tel"
                        wire:model.blur="customerPhone"
                        placeholder="1234567890"
                    >
                </div>
                @error('customerPhone')
                    <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-gray-100/50 bg-white p-4">
        <h3 class="text-sm font-bold text-gray-900">Datos para la entrega</h3>

        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Referencias del lugar
                *</label>
            <input
                class="w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                type="text"
                wire:model.blur="reference"
                placeholder="Ej. Portón negro, fachada blanca..."
            >
            @error('reference')
                <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Indicaciones para el
                repartidor</label>
            <input
                class="w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                type="text"
                wire:model.blur="deliveryInstructions"
                placeholder="Ej. Tocar timbre B, dejar en caseta..."
            >
            @error('deliveryInstructions')
                <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-amber-100/60 bg-amber-50/40 p-4">
        <h3 class="flex items-center gap-1.5 text-sm font-bold text-amber-800">
            <i class="bxf bx-restaurant text-xs"></i> Indicaciones para la cocina
        </h3>

        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-amber-600">¿Algún detalle con tu
                platillo?</label>
            <textarea
                class="w-full resize-none rounded-2xl border border-amber-200/50 bg-white px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                type="text"
                rows=4
                wire:model.blur="specialInstructions"
                placeholder="Ej. Sin cebolla, aderezo aparte..."
            >
            </textarea>
            @error('specialInstructions')
                <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-gray-100/50 bg-white p-4">
        <h3 class="text-sm font-bold text-gray-900">Método de pago</h3>

        <div class="grid grid-cols-2 gap-2 rounded-2xl border border-gray-100 bg-gray-50 p-1.5">
            <button
                class="{{ $paymentMethod === 'cash' ? 'bg-white text-emerald-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-800' }} flex cursor-pointer items-center justify-center gap-2 rounded-xl px-3 py-3 text-sm font-extrabold transition-all duration-200"
                type="button"
                wire:click="setPaymentMethod('cash')"
            >
                <i
                    class="bxf bx-money {{ $paymentMethod === 'cash' ? 'text-emerald-500' : 'text-gray-400' }} text-base"></i>
                <span>Efectivo</span>
            </button>

            <button
                class="{{ $paymentMethod === 'card' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-800' }} flex cursor-pointer items-center justify-center gap-2 rounded-xl px-3 py-3 text-sm font-extrabold transition-all duration-200"
                type="button"
                wire:click="setPaymentMethod('card')"
            >
                <i
                    class="bxf bx-credit-card {{ $paymentMethod === 'card' ? 'text-blue-500' : 'text-gray-400' }} text-base"></i>
                <span>Tarjeta</span>
            </button>
        </div>

        @if ($paymentMethod === 'cash')
            <div
                class="space-y-1 rounded-2xl border border-emerald-200/40 bg-emerald-50/80 p-4 text-left transition-all">
                <h4 class="text-sm font-bold text-emerald-950">Pago en efectivo al entregar</h4>
                <p class="text-xs font-medium leading-relaxed text-emerald-800">
                    Le entregarás el importe exacto o cambio en efectivo al repartidor al momento de recibir tu
                    pedido.
                </p>
            </div>
        @endif

        @if ($paymentMethod === 'card')
            <div
                class="gap-3.5 space-y-1 rounded-2xl border border-blue-200/50 bg-blue-50/80 p-4 text-left transition-all">
                <h4 class="text-sm font-bold text-blue-950">Pago con Terminal Física</h4>
                <p class="text-xs font-medium leading-relaxed text-blue-900">
                    El repartidor llevará una terminal bancaria inalámbrica para pagar al recibir.
                </p>
            </div>
        @endif
    </div>

    @if ($this->cart)
        <div class="shadow-xs flex flex-col gap-4 rounded-2xl border border-gray-100/50 bg-white p-4">
            <h3 class="text-sm font-bold text-gray-900">Resumen del pedido</h3>

            <div class="flex justify-between text-xs font-extrabold text-gray-500">
                <span>Productos ({{ $this->cart->items->sum('quantity') }})</span>
                <span class="font-mono text-gray-700">${{ number_format($this->cart->subtotal, 2) }}</span>
            </div>

            <div
                class="{{ is_null($this->deliveryFee) ? 'text-red-500 font-bold' : 'text-gray-500' }} flex justify-between text-xs font-extrabold">
                <span>Envío a domicilio</span>
                <span class="font-mono text-gray-700">
                    @if (is_null($this->deliveryFee))
                        Fuera de cobertura
                    @else
                        ${{ number_format($this->deliveryFee, 2) }}
                    @endif
                </span>
            </div>

            <div
                class="flex items-center justify-between border-t border-gray-100 pt-4 text-sm font-bold text-gray-900">
                <span>Total a pagar</span>
                <span
                    class="font-mono text-lg font-bold text-red-600">${{ number_format($this->totalAmount, 2) }}</span>
            </div>
        </div>
    @endif

    <div class="fixed bottom-28 left-0 right-0 z-40 px-4">
        <button
            class="flex w-full items-center justify-between rounded-2xl bg-red-500 p-4 font-bold text-white shadow-2xl shadow-gray-900/20 transition-all active:scale-[0.98] disabled:pointer-events-none disabled:bg-gray-400 disabled:opacity-40"
            wire:click="confirmPayment"
            wire:loading.attr="disabled"
            @if (is_null($this->deliveryFee)) disabled @endif
        >
            <div class="flex items-center gap-2 text-sm tracking-wide">
                <i
                    class="bxf bx-lock-alt text-xs"
                    wire:loading.remove
                    wire:target="confirmPayment"
                ></i>
                <i
                    class="bxf bx-loader-alt animate-spin text-xs"
                    wire:loading
                    wire:target="confirmPayment"
                ></i>
                <span
                    wire:loading.remove
                    wire:target="confirmPayment"
                >Hacer Pedido</span>
                <span
                    wire:loading
                    wire:target="confirmPayment"
                >Procesando...</span>
            </div>

            <div class="flex items-center gap-1 font-mono text-sm font-bold">
                <span>${{ number_format($this->totalAmount, 2) }}</span>
                <i class="bxf bx-chevron-right text-xs opacity-80"></i>
            </div>
        </button>
    </div>
</div>
