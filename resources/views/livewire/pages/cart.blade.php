<div class="flex flex-col gap-4 p-4">
    @forelse($this->carts as $index => $cart)
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
            <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
                <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                    Pedido {{ $index + 1 }} de {{ $this->carts->count() }}
                </span>
                <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                    <span>{{ $cart->business->delivery_time }} min</span>
                    <i class="bxf bx-cycling"></i>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold leading-tight text-gray-800">{{ $cart->business->name }}</h3>
                </div>

                <div class="mb-4 space-y-4">
                    @foreach ($cart->items as $item)
                        <div
                            class="group relative flex items-center justify-between gap-4 rounded-2xl border border-transparent bg-white transition-all hover:border-gray-100">

                            <div class="flex items-center gap-3">
                                <div class="h-14 w-14 flex-none overflow-hidden rounded-xl bg-gray-50">
                                    <img src="{{ $item->product_image_url_snapshot ? Storage::temporaryUrl($item->product_image_url_snapshot, now()->addMinutes(10)) : 'https://placehold.co/100x100' }}"
                                         class="h-full w-full object-cover">
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-gray-800"
                                        title="{{ $item->product_name_snapshot }}">
                                        {{ $item->product_name_snapshot }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        ${{ number_format($item->price_snapshot, 2) }} c/u
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 p-1">
                                    <button wire:click="decrementItem({{ $item->id }})" wire:loading.attr="disabled"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-xs font-bold text-gray-500 shadow-sm transition-all active:scale-90 disabled:opacity-50">
                                        @if ($item->quantity > 1)
                                            -
                                        @else
                                            <i class="bxf bx-trash text-sm text-red-400"></i>
                                        @endif
                                    </button>

                                    <span class="w-6 text-center text-xs font-bold text-gray-700"
                                        wire:target="decrementItem({{ $item->id }}), incrementItem({{ $item->id }})">
                                        {{ $item->quantity }}
                                    </span>

                                    <button wire:click="incrementItem({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-red-500 text-xs font-bold text-white shadow-sm transition-all active:scale-90 disabled:opacity-50">
                                        +
                                    </button>
                                </div>

                                <span class="text-sm font-bold text-gray-900">
                                    ${{ number_format($item->subtotal, 2) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal comida</span>
                        <span>${{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800">
                        <span class="flex items-center gap-1">
                            Envío Estimado
                            <i class="bxf bx-info-circle text-gray-400" title="Se calculará exacto al seleccionar dirección"></i>
                        </span>
                        <span>${{ number_format($this->getEstimatedDeliveryFee($cart), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center">
            <p class="text-gray-400">Tu carrito está vacío</p>
            <a href="{{ route('home') }}" wire:navigate class="mt-4 inline-block font-bold text-red-500">Ir a comer</a>
        </div>
    @endforelse

    @if ($this->carts->isNotEmpty())
        <div class="shadow-xs rounded-2xl border border-gray-100 bg-white p-5 space-y-3.5">
            <h3 class="text-sm font-bold text-gray-900">Resumen Preliminar</h3>
            
            <div class="flex justify-between text-xs font-extrabold text-gray-500">
                <span>Comida ({{ $this->carts->count() }} Rest.)</span>
                <span class="font-mono text-gray-700">${{ number_format($this->totals['subtotal'], 2) }}</span>
            </div>
            
            <div class="flex justify-between text-xs font-extrabold text-gray-500">
                <span>Envío Estimado</span>
                <span class="font-mono text-gray-700">${{ number_format($this->totals['delivery'], 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center text-sm font-bold text-gray-900 pt-3 border-t border-gray-100">
                <span>Total Aprox.</span>
                <span class="font-mono text-base text-red-600 font-bold">${{ number_format($this->totals['total'], 2) }} MXN</span>
            </div>
            
            <p class="text-center text-[10px] text-gray-400 font-semibold">El total exacto se calculará en el siguiente paso.</p>
        </div>

        <a href="{{ route('location', ['mode' => 'checkout']) }}" wire:navigate
            class="w-full rounded-2xl bg-red-500 p-4 text-center font-bold text-white transition-all active:scale-[0.98] shadow-md hover:bg-red-600 cursor-pointer">
            Seleccionar ubicación
        </a>
    @endif
</div>
