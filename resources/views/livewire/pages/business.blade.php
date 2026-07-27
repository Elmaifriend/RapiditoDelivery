@extends('layouts.page')

@section('content')
    <div class="flex flex-col gap-2 pb-20">
        <img
            class="-mt-4 h-64 min-h-64 w-full object-cover"
            src="{{ $business->banner_path ? Storage::temporaryUrl($business->banner_path, now()->addMinutes(10)) : 'https://placehold.co/256' }}"
        >

        {{-- 

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="">
                    

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>
        
        --}}

        <div class="flex flex-col items-center gap-1 pt-4">
            <h2 class="text-3xl font-bold text-gray-800">{{ $business->name }}</h2>
            <p class="text-sm text-gray-500">
                @if ($business->category)
                    {{ $business->category?->name }} •
                @endif
                <a
                    class="cursor-pointer select-none text-red-500 underline transition-transform active:scale-[0.98]"
                    href="https://wa.me/{{ $business->phone }}"
                >Contacto</a>
            </p>
        </div>

        <div class="sticky top-0 z-10 bg-gray-100">
            <div class="no-scrollbar flex items-center gap-3 overflow-x-auto p-4">
                @foreach ($business->productCategories as $category)
                    <a
                        class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 transition-transform active:scale-90"
                        href="#category-{{ $category->id }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-6">
            @foreach ($business->productCategories as $category)
                <x-ui.page-section
                    class="scroll-mt-16"
                    id="category-{{ $category->id }}"
                    title="{{ $category->name }}"
                >

                    @foreach ($category->products->where('is_active', true)->where('is_available', true) as $product)
                        <x-ui.card
                            class="flex cursor-pointer items-center justify-center transition-all active:scale-[0.98]"
                            wire:click="openProductModal({{ $product->id }})"
                        >
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-bold text-gray-800">{{ $product->name }}</h3>
                                @if ($product->description)
                                    <p class="mt-1 line-clamp-2 text-xs text-gray-400">{{ $product->description }}
                                    </p>
                                @endif
                                <span
                                    class="mt-2 block font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-gray-200">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ $product->image_path ? Storage::temporaryUrl($product->image_path, now()->addMinutes(10)) : 'https://placehold.co/150x150' }}"
                                    >
                                </div>
                                @php
                                    $itemInCart = $this->cart?->items->where('product_id', $product->id)->first();
                                @endphp
                                @if ($itemInCart)
                                    <div
                                        class="flex w-full items-center gap-2 rounded-lg bg-red-500 px-2 py-1 text-xs font-bold text-white shadow-sm">
                                        <span>-</span>
                                        <span class="w-full text-center">{{ $itemInCart->quantity }}</span>
                                        <span>+</span>
                                    </div>
                                @else
                                    <button
                                        class="w-full rounded-lg border border-red-200 px-4 py-1 text-xs font-bold text-red-500"
                                    >
                                        Agregar
                                    </button>
                                @endif
                            </div>
                        </x-ui.card>
                    @endforeach
                </x-ui.page-section>
            @endforeach
        </div>

        <div
            class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
            x-data="{ open: @entangle('showProductModal') }"
            x-show="open"
            x-cloak
        >
            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                x-show="open"
                x-transition.opacity
                x-on:click="open = false"
            ></div>
            <div
                class="relative w-full max-w-lg overflow-hidden rounded-t-3xl bg-white p-6 pb-28 shadow-xl sm:rounded-3xl"
                x-show="open"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="translate-y-0 sm:scale-100"
            >
                @if ($selectedProduct)
                    <div class="relative h-64 w-full overflow-hidden rounded-2xl bg-gray-100">
                        <img
                            class="h-full w-full object-cover"
                            src="{{ $selectedProduct->image_path ? Storage::temporaryUrl($selectedProduct->image_path, now()->addMinutes(10)) : 'https://placehold.co/600x400' }}"
                        >
                        <button
                            class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-md"
                            x-on:click="open = false"
                        >✕</button>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-xl font-bold text-gray-800">{{ $selectedProduct->name }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $selectedProduct->description }}</p>
                        <span
                            class="mt-4 block text-lg font-extrabold text-gray-900">${{ number_format($selectedProduct->price, 2) }}</span>
                    </div>
                    <div class="mt-8 flex items-center justify-between">
                        <div class="flex items-center gap-4 rounded-xl border border-gray-200 p-1">
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200"
                                wire:click="decrement"
                            >-</button>
                            <span class="w-8 text-center font-bold text-gray-800">{{ $quantity }}</span>
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200"
                                wire:click="increment"
                            >+</button>
                        </div>
                        <x-ui.button
                            class="ml-4 flex-1"
                            variant="{{ $quantity > 0 ? 'primary' : 'dark' }}"
                            wire:click="addToCart"
                        >
                            @if ($quantity > 0)
                                Agregar
                            @else
                                Quitar del pedido
                            @endif
                        </x-ui.button>
                    </div>
                @endif
            </div>
        </div>

        <div
            class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            x-data="{ open: @entangle('showStartNewCartModal') }"
            x-show="open"
            x-cloak
        >

            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                x-show="open"
                x-transition.opacity
            ></div>

            <div
                class="relative w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >

                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <span class="text-4xl">?</span>
                </div>

                <h3 class="text-lg font-bold text-gray-900">¿Empezar un nuevo carrito?</h3>

                <p class="mt-2 text-sm text-gray-500">
                    Ya tienes productos de otro negocio en tu carrito. Si continúas, se borrará tu pedido anterior para
                    agregar este.
                </p>

                <div class="mt-6 flex flex-col gap-3">
                    <x-ui.button
                        class="w-full"
                        wire:click="clearCart"
                    >
                        Nuevo pedido
                    </x-ui.button>

                    <x-ui.button
                        class="w-full"
                        variant="secondary"
                        x-on:click="open = false"
                    >
                        Cancelar
                    </x-ui.button>
                </div>
            </div>
        </div>
        <livewire:components.cart-bar />
    </div>
@endsection
