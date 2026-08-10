@extends('layouts.page')

@section('content')
    <div
        class="flex flex-col gap-4 pb-20"
        x-data="{
            map: null,
            lat: {{ $this->currentAddress?->lat ?? 32.5149 }},
            lng: {{ $this->currentAddress?->lng ?? -117.0382 }},
            showNoDriversModal: false,
        
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
                }).setView([this.lat, this.lng], 16);
        
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(this.map);
            }
        }"
        @open-no-drivers-modal.window="showNoDriversModal = true"
    >
        {{-- 

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="">
                   

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>
        
        --}}

        <x-ui.page-section>

            <x-ui.card class="flex flex-col gap-4">
                <div class="relative h-40 w-full overflow-hidden rounded-2xl border border-gray-100/50 bg-gray-100">
                    <div
                        class="absolute inset-0 z-0 h-full w-full outline-none"
                        id="delivery-map"
                        wire:ignore
                    ></div>

                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-3/4">
                        <x-ui.map-pin />
                    </div>
                </div>

                <div class="flex items-start justify-between gap-4 px-2">
                    <div class="space-y-1.5">
                        <p class="mt-1 text-sm font-bold text-gray-800">
                            {{ $this->currentAddress?->formatted_address ?? ($this->currentAddress?->address_line ?? 'Sin dirección seleccionada') }}
                        </p>
                    </div>
                    <a
                        class="pt-2 text-sm font-bold text-red-500"
                        href="{{ route('location', ['mode' => 'checkout']) }}"
                        variant="secondary"
                    >
                        Editar
                    </a>
                </div>
            </x-ui.card>

        </x-ui.page-section>

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="Datos de contacto">

                    <div class="grid grid-cols-1 gap-4">
                        <x-ui.input
                            type="text"
                            label="Tu Nombre Completo *"
                            wire:model.blur="customerName"
                            placeholder="Ej. Juan Pérez"
                            :error="$errors->first('customerName')"
                        />

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-gray-400">WhatsApp / Teléfono*</label>
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
                                    placeholder="6651234567"
                                >
                            </div>
                            @error('customerPhone')
                                <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="Datos para la entrega">

                    <div class="grid grid-cols-1 gap-4">
                        <x-ui.input
                            type="text"
                            label="Referencias del lugar *"
                            wire:model.blur="reference"
                            placeholder="Ej. Portón negro, fachada blanca..."
                            :error="$errors->first('reference')"
                        />

                        <x-ui.input
                            type="text"
                            label="Indicaciones para el repartidor"
                            wire:model.blur="deliveryInstructions"
                            placeholder="Ej. Tocar timbre B, dejar en caseta..."
                            :error="$errors->first('deliveryInstructions')"
                        />
                    </div>

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="Indicaciones para la cocina">

                    <div class="w-full space-y-1.5">
                        <label class="block text-xs font-medium text-gray-400">¿Algún detalle con tu platillo?</label>
                        <textarea
                            class="w-full resize-none rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10"
                            rows="4"
                            wire:model.blur="specialInstructions"
                            placeholder="Ej. Sin cebolla, aderezo aparte..."
                        ></textarea>
                        @if ($errors->first('specialInstructions'))
                            <span
                                class="mt-1 block text-xs font-bold text-red-500">{{ $errors->first('specialInstructions') }}</span>
                        @endif
                    </div>

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="Método de pago">

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

                        {{-- <button
                            class="{{ $paymentMethod === 'card' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-800' }} flex cursor-pointer items-center justify-center gap-2 rounded-xl px-3 py-3 text-sm font-extrabold transition-all duration-200"
                            type="button"
                            wire:click="setPaymentMethod('card')"
                        >
                            <i
                                class="bxf bx-credit-card {{ $paymentMethod === 'card' ? 'text-blue-500' : 'text-gray-400' }} text-base"></i>
                            <span>Tarjeta</span>
                        </button> --}}
                    </div>

                    @if ($paymentMethod === 'cash')
                        <div
                            class="flex items-start gap-3.5 rounded-2xl border border-emerald-200/40 bg-emerald-50/80 p-4 transition-all">
                            <div
                                class="shadow-xs flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                <i class="bxf bx-wallet text-sm"></i>
                            </div>
                            <div class="space-y-0.5 text-left">
                                <h4 class="text-xs font-bold text-emerald-950">Pago en efectivo al entregar</h4>
                                <p class="text-[11px] font-medium leading-relaxed text-emerald-800">
                                    Le entregarás el importe exacto o cambio en efectivo al repartidor al momento de recibir
                                    tu pedido.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($paymentMethod === 'card')
                        <div
                            class="flex items-start gap-3.5 rounded-2xl border border-blue-200/50 bg-blue-50/80 p-4 transition-all">
                            <div
                                class="shadow-xs flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                                <i class="bxf bx-mobile text-sm"></i>
                            </div>
                            <div class="space-y-0.5 text-left">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-blue-950">Pago con Terminal Física</h4>
                                    <span
                                        class="rounded-full bg-blue-100 px-1.5 py-0.5 text-[8px] font-bold uppercase text-blue-800"
                                    >Al recibir</span>
                                </div>
                                <p class="mt-0.5 text-[11px] font-medium leading-relaxed text-blue-900">
                                    El repartidor llevará una terminal bancaria inalámbrica para pagar al recibir.
                                </p>
                            </div>
                        </div>
                    @endif

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>

        @if ($this->cart)
            <x-ui.page-section>
                <x-ui.card padding="py-6 px-2">
                    <x-ui.page-section title="Resumen del pedido">

                        <div class="flex justify-between text-xs font-extrabold text-gray-500">
                            <span>Productos ({{ $this->cart->items->sum('quantity') }})</span>
                            <span class="font-mono text-gray-700">${{ number_format($this->cart->subtotal, 2) }}</span>
                        </div>

                        <div
                            class="{{ is_null($this->deliveryFee) ? 'text-red-500 font-black' : 'text-gray-500' }} flex justify-between text-xs font-extrabold">
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

                    </x-ui.page-section>
                </x-ui.card>
            </x-ui.page-section>
        @endif

        <div class="fixed bottom-26 left-0 right-0 z-40 px-4">
            <x-ui.button
                class="w-full"
                wire:click="confirmPayment"
                wire:loading.attr="disabled"
                :disabled="is_null($this->deliveryFee)"
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
                </div>
            </x-ui.button>
        </div>
        
        <x-ui.no-drivers-modal />
        <x-ui.max-amount-modal />
    </div>
@endsection