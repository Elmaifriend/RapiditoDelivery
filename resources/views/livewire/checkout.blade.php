{{-- Cargamos los recursos de Leaflet usando la directiva de Livewire --}}
@assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endassets

<div class="flex flex-col gap-4 p-4"
     x-data="{
        map: null,
        statusText: 'Centrando en Tijuana...',

        init() {
            this.$nextTick(() => { this.initMap(); });
        },

        initMap() {
            if (this.map) return;
            var defaultLat = 32.5149;
            var defaultLng = -117.0382;

            this.map = L.map('delivery-map', {
                zoomControl: false, dragging: true, scrollWheelZoom: 'center'
            }).setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '', subdomains: 'abcd', maxZoom: 20
            }).addTo(this.map);

            this.locateUser();
            this.setupListeners();
        },

        locateUser() {
            if ('geolocation' in navigator) {
                this.statusText = 'Buscando GPS...';
                navigator.geolocation.getCurrentPosition(
                    (position) => { this.flyTo(position.coords.latitude, position.coords.longitude, 'Tu ubicación actual'); },
                    (error) => { this.statusText = 'Tijuana (Default)'; },
                    { enableHighAccuracy: true, timeout: 4000 }
                );
            }
        },

        setupListeners() {
            this.map.on('move', () => { this.statusText = 'Moviendo...'; });
            this.map.on('moveend', () => {
                var center = this.map.getCenter();
                this.statusText = center.lat.toFixed(4) + ', ' + center.lng.toFixed(4);
            });
        },

        flyTo(lat, lng, label) {
            this.map.flyTo([lat, lng], 16, { animate: true, duration: 1.5 });
            this.statusText = label;
        }
     }"
>

    <div class="flex items-center gap-4">
        <h2 class="text-2xl font-bold">Checkout</h2>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 p-3 rounded-xl text-sm font-bold">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mb-6">
        <h3 class="mb-3 ml-1 text-lg font-bold">Dirección de entrega</h3>

        <div class="group relative mb-4 h-64 w-full overflow-hidden rounded-[1.5rem] border border-gray-200 bg-gray-100">
            <div id="delivery-map" wire:ignore class="absolute inset-0 z-0 h-full w-full outline-none"></div>

            <div class="pointer-events-none absolute left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 pb-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600 drop-shadow-lg filter" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
                <div class="absolute -bottom-1 left-1/2 h-2 w-6 -translate-x-1/2 rounded-[100%] bg-black/20 blur-[2px]"></div>
            </div>

            <div class="absolute bottom-4 left-1/2 z-40 -translate-x-1/2 whitespace-nowrap rounded-full border border-gray-200 bg-white/95 px-4 py-2 text-[10px] font-bold text-gray-600 shadow-sm backdrop-blur-sm">
                <span x-text="statusText"></span>
            </div>
        </div>

        <div class="space-y-3">
            {{-- Renderizado dinámico de las direcciones del usuario --}}
            @forelse($this->addresses as $address)
                <button 
                    @click="flyTo({{ $address->lat }}, {{ $address->lng }}, '{{ $address->label ?? 'Dirección' }}')"
                    wire:click="selectAddress({{ $address->id }})"
                    class="flex w-full items-center gap-3 rounded-[1.5rem] border p-3 text-left transition-transform active:scale-95
                           {{ $selectedAddressId === $address->id ? 'border-red-500 bg-red-50/50' : 'border-gray-200 bg-white hover:bg-gray-50' }}"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-100 {{ $selectedAddressId === $address->id ? 'bg-white text-red-500' : 'bg-gray-50 text-gray-400' }}">
                        <i class="fas {{ $address->label === 'Casa' ? 'fa-home' : ($address->label === 'Oficina' ? 'fa-briefcase' : 'fa-map-marker-alt') }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-gray-800">{{ $address->label ?? 'Dirección' }}</p>
                        </div>
                        <p class="line-clamp-1 text-[10px] text-gray-500">{{ $address->formatted_address ?? $address->address_line }}</p>
                    </div>
                    @if($selectedAddressId === $address->id)
                        <div class="flex h-5 w-5 items-center justify-center rounded-full border-[5px] border-red-500 bg-white"></div>
                    @else
                        <div class="h-5 w-5 rounded-full border border-gray-300"></div>
                    @endif
                </button>
            @empty
                <p class="text-sm text-gray-500">No tienes direcciones guardadas. Por favor, agrega una nueva.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        <h3 class="mb-3 ml-1 text-lg font-bold">Instrucciones Adicionales</h3>
        <input type="text" wire:model="specialInstructions" placeholder="Instrucciones (ej. tocar timbre B)..."
            class="input-flat w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm transition-colors focus:border-red-300 focus:outline-none">
    </div>

    <div class="mb-8">
        <h3 class="mb-3 ml-1 text-lg font-bold">Método de Pago</h3>

        <div class="space-y-4 rounded-[2rem] border border-gray-200 bg-white p-5">
            <div class="mb-2 flex gap-3">
                <button wire:click="setPaymentMethod('card')"
                    class="flex-1 rounded-xl border py-2.5 text-sm font-bold transition-transform active:scale-90 
                    {{ $paymentMethod === 'card' ? 'border-red-500 bg-red-500 text-white' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50' }}">
                    Tarjeta
                </button>
                <button wire:click="setPaymentMethod('cash')"
                    class="flex-1 rounded-xl border py-2.5 text-sm font-bold transition-transform active:scale-90
                    {{ $paymentMethod === 'cash' ? 'border-red-500 bg-red-500 text-white' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50' }}">
                    Efectivo
                </button>
            </div>

            @if($paymentMethod === 'card')
                <div>
                    <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">Número de Tarjeta</label>
                    <div class="relative">
                        <input type="text" wire:model="cardNumber" placeholder="0000 0000 0000 0000"
                            class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 font-mono font-bold text-gray-700 focus:outline-none">
                        <i class="fab fa-cc-visa absolute right-4 top-4 text-xl text-blue-800"></i>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">Fecha Exp.</label>
                        <input type="text" wire:model="cardExpiry" placeholder="MM/AA"
                            class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center font-mono font-bold text-gray-700 focus:outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">CVV</label>
                        <input type="password" wire:model="cardCvv" placeholder="***"
                            class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center font-mono font-bold text-gray-700 focus:outline-none">
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($this->cart)
        <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5">
            <div class="mb-1.5 flex justify-between text-xs text-gray-500">
                <span>Comida</span>
                <span>${{ number_format($this->cart->subtotal, 2) }}</span>
            </div>
            
            {{-- Muestra si está fuera de cobertura o el costo exacto --}}
            <div class="mb-1.5 flex justify-between text-xs font-bold {{ is_null($this->deliveryFee) ? 'text-red-500' : 'text-gray-500' }}">
                <span>Envío Exacto</span>
                <span>
                    @if(is_null($this->deliveryFee))
                        Fuera de cobertura
                    @else
                        ${{ number_format($this->deliveryFee, 2) }}
                    @endif
                </span>
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-800 mt-4 border-t border-gray-100 pt-3">
                <span>Total a pagar</span>
                <div class="flex flex-col items-end">
                    <span>${{ number_format($this->totalAmount, 2) }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Deshabilita el botón si la dirección no tiene cobertura (deliveryFee es null) --}}
    <button wire:click="confirmPayment" wire:loading.attr="disabled"
        @if(is_null($this->deliveryFee)) disabled @endif
        class="flex w-full items-center justify-center gap-2 rounded-2xl py-4 font-bold text-white opacity-95 transition-transform hover:opacity-100 active:scale-90 disabled:opacity-50 disabled:cursor-not-allowed {{ is_null($this->deliveryFee) ? 'bg-gray-400' : 'bg-red-500' }}">
        <i class="fas fa-lock" wire:loading.remove wire:target="confirmPayment"></i>
        <i class="fas fa-spinner fa-spin" wire:loading wire:target="confirmPayment"></i>
        <span wire:loading.remove wire:target="confirmPayment">
            Confirmar Pago {{ $this->cart ? 'de '.$this->cart->items->count().' Productos' : '' }}
        </span>
        <span wire:loading wire:target="confirmPayment">Procesando...</span>
    </button>
    
    <p class="mt-4 pb-4 text-center text-[10px] text-gray-400">La transacción es segura y está encriptada.</p>
</div>