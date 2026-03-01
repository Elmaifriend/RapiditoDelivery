<?php

use Livewirte\Attributes\Title;
use Livewire\Component;

new #[Title('Restaurant')] class extends Component {
    //
};
?>

<div>
    {{-- Walk as if you are kissing the Earth with your feet. - Thich Nhat Hanh --}}
    <div class="relative h-64 w-full">
        <img src="https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop"
            class="h-full w-full object-cover">

        <div class="bg-linear-to-t absolute bottom-0 left-0 right-0 h-32 from-gray-100 to-transparent"></div>

        <div class="absolute -bottom-16 left-0 right-0 px-5">
            <div
                class="flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-lg shadow-gray-200/50">
                <h2 class="mb-1 text-2xl font-bold text-gray-800">Burger King</h2>
                <p class="mb-3 text-xs font-medium text-gray-500">
                    Comida Rápida • 25-35 min
                </p>
                <div class="flex gap-4 text-xs font-bold">
                    <span class="rounded-full border border-red-100 bg-red-50 px-3 py-1.5 text-red-500">
                        Envío $25.00
                    </span>
                    <span
                        class="flex items-center gap-1 rounded-full border border-amber-100 bg-amber-50 px-3 py-1.5 text-amber-600">
                        4.8 <i class="bxf bx-star text-[10px]"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-20 px-5">
        <div class="no-scrollbar mb-4 flex gap-3 overflow-x-auto bg-white p-4 rounded-2xl">
            <button
                class="whitespace-nowrap rounded-full border border-red-500 bg-red-500 px-5 py-2.5 text-xs font-bold text-white active:scale-90 transition-transform">
                Hamburguesas
            </button>

            <button
                class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2.5 text-xs font-bold text-gray-500 active:scale-90 transition-transform">
                Bebidas
            </button>

            <button
                class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2.5 text-xs font-bold text-gray-500 active:scale-90 transition-transform">
                Complementos
            </button>
        </div>

        <div class="space-y-6 pt-2">

            <div>
                <h3 class="mb-3 ml-1 text-lg font-bold text-gray-800">Hamburguesas</h3>
                <div class="space-y-4">

                    <div
                        class="flex items-center justify-between rounded-[1.2rem] border border-gray-100 bg-white p-2.5 transition-colors hover:border-gray-200">
                        <div class="flex-1 pl-1 pr-4">
                            <h4 class="text-sm font-bold text-gray-800">Doble Queso Bacon</h4>
                            <p class="mb-2 mt-1 line-clamp-2 text-xs font-medium text-gray-400">
                                Doble carne a la parrilla, queso americano, tocino crujiente y salsa BBQ.
                            </p>
                            <span class="font-extrabold text-gray-900">$145.00</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="relative h-20 w-20 overflow-hidden rounded-2xl bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?auto=format&fit=crop&w=150&q=80"
                                    class="h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-black/5"></div>
                            </div>
                            <button
                                class="w-full rounded-xl border border-red-200 bg-white px-4 py-1.5 text-[10px] font-bold text-red-500 active:scale-90 transition-transform">
                                Agregar
                            </button>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between rounded-[1.2rem] border border-gray-100 bg-white p-2.5">
                        <div class="flex-1 pl-1 pr-4">
                            <h4 class="text-sm font-bold text-gray-800">Clásica Whopper</h4>
                            <p class="mb-2 mt-1 line-clamp-2 text-xs font-medium text-gray-400">
                                La favorita de todos con lechuga fresca, tomate y mayonesa.
                            </p>
                            <span class="font-extrabold text-gray-900">$120.00</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="relative h-20 w-20 overflow-hidden rounded-2xl bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=150&q=80"
                                    class="h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-black/5"></div>
                            </div>
                            <button
                                class="w-full rounded-xl border border-red-200 bg-white px-4 py-1.5 text-[10px] font-bold text-red-500 active:scale-90 transition-transform">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <h3 class="mb-3 ml-1 text-lg font-bold text-gray-800">Bebidas</h3>
                <div class="space-y-4">

                    <div
                        class="flex items-center justify-between rounded-[1.2rem] border border-gray-100 bg-white p-2.5 transition-colors hover:border-gray-200">
                        <div class="flex-1 pl-1 pr-4">
                            <h4 class="text-sm font-bold text-gray-800">Coca-Cola Grande</h4>
                            <p class="mb-2 mt-1 line-clamp-2 text-xs font-medium text-gray-400">
                                Refresco de cola bien frío con hielo.
                            </p>
                            <span class="font-extrabold text-gray-900">$45.00</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="relative h-20 w-20 overflow-hidden rounded-2xl bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=150&q=80"
                                    class="h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-black/5"></div>
                            </div>
                            <button
                                class="w-full rounded-xl border border-red-200 bg-white px-4 py-1.5 text-[10px] font-bold text-red-500 active:scale-90 transition-transform">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <h3 class="mb-3 ml-1 text-lg font-bold text-gray-800">Complementos</h3>
                <div class="space-y-4">

                    <div
                        class="flex items-center justify-between rounded-[1.2rem] border border-gray-100 bg-white p-2.5 transition-colors hover:border-gray-200">
                        <div class="flex-1 pl-1 pr-4">
                            <h4 class="text-sm font-bold text-gray-800">Papas Fritas</h4>
                            <p class="mb-2 mt-1 line-clamp-2 text-xs font-medium text-gray-400">
                                Papas corte clásico con sal de mar.
                            </p>
                            <span class="font-extrabold text-gray-900">$55.00</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="relative h-20 w-20 overflow-hidden rounded-2xl bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?auto=format&fit=crop&w=150&q=80"
                                    class="h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-black/5"></div>
                            </div>
                            <button
                                class="w-full rounded-xl border border-red-200 bg-white px-4 py-1.5 text-[10px] font-bold text-red-500 active:scale-90 transition-transform">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
