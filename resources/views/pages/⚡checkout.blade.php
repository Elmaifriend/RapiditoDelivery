<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Checkout')] class extends Component {
    //
};
?>

{{-- Cargamos los recursos de Leaflet usando la directiva de Livewire --}}
@assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endassets

{{-- Inicializamos Alpine aquí. Toda la lógica del mapa vive dentro de este objeto --}}
<div class="flex flex-col gap-4 p-4"
     x-data="{
        map: null,
        statusText: 'Centrando en Tijuana...',

        init() {
            // Esperar un tick para asegurar que el DOM esté listo
            this.$nextTick(() => {
                this.initMap();
            });
        },

        initMap() {
            // Evitar reinicializar si ya existe
            if (this.map) return;

            var defaultLat = 32.5149;
            var defaultLng = -117.0382;

            this.map = L.map('delivery-map', {
                zoomControl: false,
                dragging: true,
                scrollWheelZoom: 'center'
            }).setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(this.map);

            this.locateUser();
            this.setupListeners();
        },

        locateUser() {
            if ('geolocation' in navigator) {
                this.statusText = 'Buscando GPS...';
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.flyTo(position.coords.latitude, position.coords.longitude, 'Tu ubicación actual');
                    },
                    (error) => {
                        this.statusText = 'Tijuana (Default)';
                    },
                    { enableHighAccuracy: true, timeout: 4000 }
                );
            }
        },

        setupListeners() {
            this.map.on('move', () => {
                this.statusText = 'Moviendo...';
            });
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

    <div class="mb-6">
        <h3 class="mb-3 ml-1 text-lg font-bold">Dirección de entrega</h3>

        <div class="group relative mb-4 h-64 w-full overflow-hidden rounded-[1.5rem] border border-gray-200 bg-gray-100">

            {{-- IMPORTANTE: wire:ignore previene que Livewire borre el mapa al actualizarse --}}
            <div id="delivery-map" wire:ignore class="absolute inset-0 z-0 h-full w-full outline-none"></div>

            <div class="pointer-events-none absolute left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 pb-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600 drop-shadow-lg filter"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z"
                        clip-rule="evenodd" />
                </svg>
                <div class="absolute -bottom-1 left-1/2 h-2 w-6 -translate-x-1/2 rounded-[100%] bg-black/20 blur-[2px]">
                </div>
            </div>

            {{-- Usamos x-text para vincular el texto reactivamente --}}
            <div
                class="absolute bottom-4 left-1/2 z-40 -translate-x-1/2 whitespace-nowrap rounded-full border border-gray-200 bg-white/95 px-4 py-2 text-[10px] font-bold text-gray-600 shadow-sm backdrop-blur-sm">
                <span x-text="statusText"></span>
            </div>
        </div>

        <div class="space-y-3">
            {{-- Usamos @click de Alpine para llamar a la función interna --}}
            <button @click="flyTo(32.5312, -117.1226, 'Casa - Playas')"
                class="flex w-full items-center gap-3 rounded-[1.5rem] border border-red-200 bg-red-50/50 p-3 text-left transition-transform active:scale-95">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-red-100 bg-white text-red-500">
                    <i class="fas fa-home"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-gray-800">Casa</p>
                        <span class="rounded bg-red-100 px-1.5 py-0.5 text-[9px] font-bold text-red-500">Default</span>
                    </div>
                    <p class="line-clamp-1 text-[10px] text-gray-500">Paseo Ensenada 100, Playas de Tijuana</p>
                </div>
                <div class="flex h-5 w-5 items-center justify-center rounded-full border-[5px] border-red-500 bg-white">
                </div>
            </button>

            <button @click="flyTo(32.5255, -117.0145, 'Oficina - Zona Río')"
                class="flex w-full items-center gap-3 rounded-[1.5rem] border border-gray-200 bg-white p-3 text-left transition-transform hover:bg-gray-50 active:scale-95">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 text-gray-400">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-800">Oficina</p>
                    <p class="line-clamp-1 text-[10px] text-gray-500">Blvd. Sánchez Taboada, Zona Río</p>
                </div>
                <div class="h-5 w-5 rounded-full border border-gray-300"></div>
            </button>

            <button
                class="flex w-full items-center justify-center gap-2 rounded-[1.5rem] border border-dashed border-gray-300 bg-gray-50 p-3 text-xs font-bold text-gray-500 transition-transform hover:bg-gray-100 active:scale-95">
                <i class="fas fa-plus"></i>
                <span>Agregar nueva dirección</span>
            </button>
        </div>
    </div>

    <div class="mt-3">
        <h3 class="mb-3 ml-1 text-lg font-bold">Instrucciones Adicionales</h3>

        <input type="text" placeholder="Instrucciones (ej. tocar timbre B)..."
            class="input-flat w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm transition-colors focus:border-red-300 focus:outline-none">
    </div>

    <div class="mb-8">
        <h3 class="mb-3 ml-1 text-lg font-bold">Método de Pago</h3>

        <div class="space-y-4 rounded-[2rem] border border-gray-200 bg-white p-5">
            <div class="mb-2 flex gap-3">
                <button
                    class="flex-1 rounded-xl border border-red-500 bg-red-500 py-2.5 text-sm font-bold text-white transition-transform active:scale-90">Tarjeta</button>
                <button
                    class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-bold text-gray-500 transition-transform hover:bg-gray-50 active:scale-90">Efectivo</button>
            </div>

            <div>
                <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">Número de Tarjeta</label>
                <div class="relative">
                    <input type="text" value="4520 1234 5678 9010"
                        class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 font-mono font-bold text-gray-700 focus:outline-none">
                    <i class="fab fa-cc-visa absolute right-4 top-4 text-xl text-blue-800"></i>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">Fecha Exp.</label>
                    <input type="text" value="09/28"
                        class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center font-mono font-bold text-gray-700 focus:outline-none">
                </div>
                <div class="flex-1">
                    <label class="mb-1.5 ml-1 block text-xs font-bold text-gray-500">CVV</label>
                    <input type="password" value="***"
                        class="input-flat w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center font-mono font-bold text-gray-700 focus:outline-none">
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5">
        <div class="mb-1.5 flex justify-between text-xs text-gray-500">
            <span>Comida</span>
            <span>$320.00</span>
        </div>
        <div class="mb-1.5 flex justify-between text-xs text-gray-500">
            <span>Envíos (x3)</span>
            <span>$85.00</span>
        </div>
        <div class="mb-3 flex justify-between border-b border-gray-100 pb-3 text-xs text-gray-500">
            <span>Servicio</span>
            <span>$20.00</span>
        </div>
        <div class="flex justify-between text-lg font-bold text-gray-800">
            <span>Total a pagar</span>
            <div class="flex flex-col items-end">
                <span>$425.00</span>
                <span class="text-sm text-gray-500">$24.52 USD</span>
            </div>
        </div>
    </div>

    <button
        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-red-500 py-4 font-bold text-white opacity-95 transition-transform hover:opacity-100 active:scale-90">
        <i class="fas fa-lock"></i>
        <span>Confirmar Pago de 3 Pedidos</span>
    </button>
    <p class="mt-4 pb-4 text-center text-[10px] text-gray-400">La transacción es segura y está encriptada.</p>
</div>
