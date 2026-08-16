<div class="bottom-26 absolute left-0 right-0 z-40 flex justify-center px-4 pointer-events-none">
    @if ($this->cart && $this->cart->items->count())
        <div class="pointer-events-auto flex w-full max-w-[900px] items-center justify-between rounded-2xl bg-white px-5 py-4 shadow-2xl">

            <div>
                <span class="text-xs font-semibold text-gray-500">
                    {{ $this->cart->items->sum('quantity') }} productos
                </span>

                <div class="font-mono text-lg font-bold">
                    ${{ number_format($this->cart->total, 2) }}
                </div>
            </div>

            <x-ui.button wire:click="goToLocation" class="px-8!">
                Comprar
            </x-ui.button>
        </div>
    @endif
</div>
