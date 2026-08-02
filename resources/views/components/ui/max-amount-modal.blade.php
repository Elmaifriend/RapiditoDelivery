@props(['maxAmount' => 500])

<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    @open-max-amount-modal.window="open = true"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div 
        @click.away="open = false"
        class="w-full max-w-sm p-6 bg-white rounded-3xl shadow-xl space-y-4 text-center transform transition-all"
    >
        {{-- Ícono con badge estilizado --}}
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
            <i class="bx bx-error-circle text-2xl"></i>
        </div>

        {{-- Encabezado y Explicación --}}
        <div class="space-y-2">
            <h3 class="text-base font-bold text-gray-900">Límite de compra alcanzado</h3>
            <p class="text-xs text-gray-500 leading-relaxed">
                ¡Por apertura, la cantidad máxima permitida por pedido es de <strong class="text-gray-800">${{ number_format($maxAmount, 2) }} MXN</strong>!
            </p>
            <p class="text-xs text-gray-400 leading-relaxed">
                A medida que realices más pedidos en la plataforma, tu límite irá aumentando automáticamente. Te sugerimos dividir tu compra en dos pedidos.
            </p>
        </div>

        {{-- Botón de Cierre --}}
        <button
            type="button"
            @click="open = false"
            class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-2xl transition-all cursor-pointer"
        >
            Entendido
        </button>
    </div>
</div>