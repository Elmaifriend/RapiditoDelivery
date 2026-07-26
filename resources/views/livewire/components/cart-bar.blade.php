<div class="fixed bottom-24 left-0 right-0 z-40 flex justify-center px-4">
    @if($this->cart && $this->cart->items->count())
    <div class="flex w-full max-w-[900px] items-center justify-between rounded-2xl bg-gray-900 px-5 py-4 text-white shadow-2xl">

        <div>
            <span class="text-xs text-gray-300 font-semibold">
                {{ $this->cart->items->sum('quantity') }} productos
            </span>

            <div class="text-lg font-bold font-mono">
                ${{ number_format($this->cart->total, 2) }}
            </div>
        </div>

        <button 
            wire:click="goToLocation" 
            class="rounded-xl bg-red-500 px-6 py-2 font-bold active:scale-[0.98] transition-all cursor-pointer"
        >
            Comprar
        </button>

    </div>
    @endif
</div>
