@extends('layouts.page')

@section('content')
    @forelse($this->carts as $index => $cart)
        <x-ui.page-section>
            <x-ui.card padding="p-0">
                <div class="flex items-center justify-between bg-red-500 p-3 px-5 text-white">
                    <span class="text-sm font-bold text-white">
                        Pedido
                    </span>
                    @if ($cart->business->delivery_time)
                        <div class="flex items-center gap-1.5 text-xs font-medium text-white">
                            <span>{{ $cart->business->delivery_time }} min</span>
                            <i class="bxf bx-cycling"></i>
                        </div>
                    @endif
                </div>

                <x-ui.page-section
                    class="px-6 py-4"
                    title="{{ $cart->business->name }}"
                    subtitle="Productos"
                >
                    <div class="space-y-4">
                        @foreach ($cart->items as $item)
                            <div
                                class="group relative flex items-center justify-between gap-4 rounded-2xl border border-transparent bg-white transition-all hover:border-gray-100">

                                <div class="flex items-center gap-3">
                                    <div class="h-14 w-14 flex-none overflow-hidden rounded-xl bg-gray-50">
                                        <img
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                            src="{{ $item->product_image_url_snapshot ? Storage::temporaryUrl($item->product_image_url_snapshot, now()->addMinutes(10)) : 'https://placehold.co/100x100' }}"
                                        >
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-bold text-gray-800"
                                            title="{{ $item->product_name_snapshot }}"
                                        >
                                            {{ $item->product_name_snapshot }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            ${{ number_format($item->price_snapshot, 2) }} c/u
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <div class="flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 p-1">
                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-xs font-bold text-gray-500 shadow-sm transition-all active:scale-90 disabled:opacity-50"
                                            wire:click="decrementItem({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                        >
                                            @if ($item->quantity > 1)
                                                -
                                            @else
                                                <i class="bxf bx-trash text-sm text-red-400"></i>
                                            @endif
                                        </button>

                                        <span
                                            class="w-6 text-center text-xs font-bold text-gray-700"
                                            wire:target="decrementItem({{ $item->id }}), incrementItem({{ $item->id }})"
                                        >
                                            {{ $item->quantity }}
                                        </span>

                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-md bg-red-500 text-xs font-bold text-white shadow-sm transition-all active:scale-90 disabled:opacity-50"
                                            wire:click="incrementItem({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                        >
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
                            <span>
                                Envío Estimado
                            </span>
                            <span>${{ number_format($this->getEstimatedDeliveryFee($cart), 2) }}</span>
                        </div>
                    </div>
                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>
    @empty
        <div class="py-20 text-center">
            <p class="text-gray-400">Tu carrito está vacío</p>
            <a
                class="mt-4 inline-block font-bold text-red-500"
                href="{{ route('home') }}"
                wire:navigate
            >Ir a comer</a>
        </div>
    @endforelse

    @if ($this->carts->isNotEmpty())
        <x-ui.page-section>
            <x-ui.card padding="p-0">
                <div class="flex items-center justify-between bg-red-500 p-3 px-5 text-white">
                    <span class="text-sm font-bold text-white">
                        Resumen
                    </span>
                </div>

                <x-ui.page-section class="px-6 py-4">
                    <div class="flex justify-between text-xs font-extrabold text-gray-500">
                        <span>Productos</span>
                        <span class="font-mono text-gray-700">${{ number_format($this->totals['subtotal'], 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xs font-extrabold text-gray-500">
                        <span>Envío Estimado</span>
                        <span class="font-mono text-gray-700">${{ number_format($this->totals['delivery'], 2) }}</span>
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-gray-100 pt-3 text-sm font-bold text-gray-900">
                        <span>Total Aprox.</span>
                        <span
                            class="font-mono text-base font-bold text-red-600">${{ number_format($this->totals['total'], 2) }}
                            MXN</span>
                    </div>

                    <p class="text-center text-[10px] font-semibold text-gray-400">El total exacto se calculará en el
                        siguiente
                        paso.</p>
                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>

        <x-ui.page-section>
            <x-ui.button href="{{ route('location', ['mode' => 'checkout']) }}">
                Seleccionar ubicación
            </x-ui.button>
        </x-ui.page-section>
    @endif
@endsection
