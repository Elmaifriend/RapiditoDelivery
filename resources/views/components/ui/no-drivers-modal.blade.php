@props(['show' => false])

<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    @open-no-drivers-modal.window="open = true"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div 
        @click.away="open = false"
        class="w-full max-w-sm p-6 bg-white rounded-3xl shadow-xl space-y-4 text-center transform transition-all"
    >
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
            <i class="bxf bx-time-five text-2xl"></i>
        </div>

        <div class="space-y-2">
            <h3 class="text-base font-bold text-gray-900">Sin repartidores disponibles</h3>
            <p class="text-xs text-gray-500 leading-relaxed">
                Ahorita no hay repartidores disponibles, te recomendamos intentarlo después de las 10:00 am.
            </p>
        </div>

        <button
            type="button"
            @click="open = false"
            class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-2xl transition-all cursor-pointer"
        >
            Entendido
        </button>
    </div>
</div>