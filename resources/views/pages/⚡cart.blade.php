<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Cart')] class extends Component {
    //
};
?>

<div class="flex flex-col gap-4 p-4">
    {{-- The whole future lies in uncertainty: live immediately. - Seneca --}}

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
        <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
            <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">Pedido 1 de
                3</span>
            <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                <span>25 min</span>
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>

        <div class="p-5">
            <div class="mb-4 border-b border-gray-50 pb-4">
                <h3 class="text-lg font-bold leading-tight text-gray-800">El Tizoncito</h3>
                <p class="flex items-center gap-1 text-[10px] font-bold text-green-500"><i
                        class="fas fa-circle text-[6px]"></i> Abierto ahora</p>
            </div>

            <div class="mb-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                            2x</div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">Tacos al Pastor</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-800">$130.00</span>
                </div>
            </div>

            <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal comida</span>
                    <span>$130.00</span>
                </div>
                <div class="flex justify-between font-bold text-gray-800">
                    <span class="flex items-center gap-1"><i class="bxf bx-cycling text-lg text-gray-400"></i> Envío
                        (Repartidor
                        1)</span>
                    <span>$25.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
        <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
            <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">Pedido 2 de
                3</span>
            <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                <span>45 min</span>
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>

        <div class="p-5">
            <div class="mb-4 border-b border-gray-50 pb-4">
                <h3 class="text-lg font-bold leading-tight text-gray-800">Sushi Roll</h3>
                <p class="flex items-center gap-1 text-[10px] font-bold text-green-500"><i
                        class="fas fa-circle text-[6px]"></i> Abierto ahora</p>
            </div>

            <div class="mb-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                            1x</div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">California Roll</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-800">$90.00</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                            1x</div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">Kushiages</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-800">$55.00</span>
                </div>
            </div>
            <!-- Costos específicos de este pedido -->
            <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal comida</span>
                    <span>$145.00</span>
                </div>
                <div class="flex justify-between font-bold text-gray-800">
                    <span class="flex items-center gap-1"><i class="bxf bx-cycling text-lg text-gray-400"></i> Envío
                        (Repartidor
                        2)</span>
                    <span>$40.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
        <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
            <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">Pedido 3 de
                3</span>
            <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                <span>30 min</span>
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>

        <div class="p-5">
            <div class="mb-4 border-b border-gray-50 pb-4">
                <h3 class="text-lg font-bold leading-tight text-gray-800">Pizza Maestra</h3>
                <p class="flex items-center gap-1 text-[10px] font-bold text-green-500"><i
                        class="fas fa-circle text-[6px]"></i> Abierto ahora</p>
            </div>

            <div class="mb-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                            1x</div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">Refresco 2L</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-800">$45.00</span>
                </div>
            </div>
            <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal comida</span>
                    <span>$45.00</span>
                </div>
                <div class="flex justify-between font-bold text-gray-800">
                    <span class="flex items-center gap-1"><i class="bxf bx-cycling text-lg text-gray-400"></i> Envío
                        (Repartidor
                        3)</span>
                    <span>$20.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-4xl bg-white p-6">
        <h3 class="mb-4 text-lg font-bold text-gray-800">Resumen de Pagos</h3>

        <div class="mb-2 flex justify-between px-2.5 text-sm text-gray-600">
            <span>Comida (3 Rest.)</span>
            <span>$320.00</span>
        </div>

        <div
            class="mb-2 flex justify-between rounded-xl border border-red-100 bg-red-50 p-2.5 text-sm font-bold text-red-600">
            <span class="flex items-center gap-2"><i class="bxf bx-motorcycle text-lg"></i> Envíos (3
                repartidores)</span>
            <span>$85.00</span>
        </div>

        <div class="mb-4 flex justify-between px-2.5 text-sm text-gray-600">
            <span>Tarifa de servicio</span>
            <span>$20.00</span>
        </div>

        <div class="flex justify-between border-t border-gray-100 pt-4 text-2xl font-bold text-gray-900">
            <span>Total Final</span>
            <div class="text-end">
                <p>$425.00 MXN</p>
                <p class="text-sm text-gray-500">$24.52 USD</p>
            </div>
        </div>
        <p class="mt-2 text-center text-[10px] text-gray-400">Incluye impuestos y tarifas de la plataforma.</p>
    </div>

    <a
        href="{{ route('checkout') }}" wire:navigate
        class="w-full rounded-2xl bg-red-500 px-6 py-4 font-bold text-white transition-all active:scale-90 active:bg-red-400">
        Pagar Pedidos
    </a>
</div>
