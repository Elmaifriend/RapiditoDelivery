<div class="min-h-screen bg-gray-50 pb-28">
    <div class="relative h-52 w-full overflow-hidden bg-gray-200">
        <img src="{{ $business->banner_path ? Storage::temporaryUrl($business->banner_path, now()->addMinutes(10)) : 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop' }}"
            class="h-full w-full object-cover">
    </div>

    <div class="bg-white px-5 pb-5 pt-4">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $business->name }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ $business->category?->name }} • 25-35 min
        </p>
    </div>

    <div class="sticky top-0 z-10 bg-gray-50">
        <div class="no-scrollbar flex items-center gap-3 overflow-x-auto p-4">
            @foreach ($business->productCategories as $category)
                <a href="#category-{{ $category->id }}"
                    class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 transition-transform active:scale-90">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="space-y-8 px-4 pt-4">
        @foreach ($business->productCategories as $category)
            <div id="category-{{ $category->id }}">
                <h2 class="mb-3 text-lg font-bold text-gray-800">
                    {{ $category->name }}
                </h2>
                <div class="space-y-4">
                    @foreach ($category->products->where('is_active', true)->where('is_available', true) as $product)
                        <div wire:click="openProductModal({{ $product->id }})"
                            class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-100 bg-white p-3 transition-all active:scale-[0.98]">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-bold text-gray-800">{{ $product->name }}</h3>
                                @if ($product->description)
                                    <p class="mt-1 line-clamp-2 text-xs text-gray-400">{{ $product->description }}</p>
                                @endif
                                <span
                                    class="mt-2 block font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-gray-200">
                                    <img src="{{ $product->image_path ? Storage::temporaryUrl($product->image_path, now()->addMinutes(10)) : 'https://placehold.co/150x150' }}"
                                        class="h-full w-full object-cover">
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
                                        class="w-full rounded-lg border border-red-200 px-4 py-1 text-xs font-bold text-red-500">
                                        Agregar
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div x-data="{ open: @entangle('showProductModal') }" x-show="open" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
        x-cloak>
        <div x-show="open" x-transition.opacity x-on:click="open = false"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 sm:scale-100"
            class="relative w-full max-w-lg overflow-hidden rounded-t-3xl bg-white p-6 pb-28 shadow-xl sm:rounded-3xl">
            @if ($selectedProduct)
                <div class="relative h-64 w-full overflow-hidden rounded-2xl bg-gray-100">
                    <img src="{{ $selectedProduct->image_path ? Storage::temporaryUrl($selectedProduct->image_path, now()->addMinutes(10)) : 'https://placehold.co/600x400' }}"
                        class="h-full w-full object-cover">
                    <button x-on:click="open = false"
                        class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-md">✕</button>
                </div>
                <div class="mt-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ $selectedProduct->name }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $selectedProduct->description }}</p>
                    <span
                        class="mt-4 block text-lg font-extrabold text-gray-900">${{ number_format($selectedProduct->price, 2) }}</span>
                </div>
                <div class="mt-8 flex items-center justify-between">
                    <div class="flex items-center gap-4 rounded-xl border border-gray-200 p-1">
                        <button wire:click="decrement"
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">-</button>
                        <span class="w-8 text-center font-bold text-gray-800">{{ $quantity }}</span>
                        <button wire:click="increment"
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">+</button>
                    </div>
                    <button wire:click="addToCart"
                        class="{{ $quantity > 0 ? 'bg-red-500' : 'bg-gray-800' }} ml-4 flex-1 rounded-xl py-3 font-bold text-white shadow-lg transition-all active:scale-95">
                        @if ($quantity > 0)
                            Agregar
                        @else
                            Quitar del pedido
                        @endif
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div x-data="{ open: @entangle('showStartNewCartModal') }" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4"
        x-cloak>

        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">

            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="bxf bx-error-circle text-4xl"></i>
            </div>

            <h3 class="text-lg font-bold text-gray-900">¿Empezar un nuevo carrito?</h3>

            <p class="mt-2 text-sm text-gray-500">
                Ya tienes productos de otro negocio en tu carrito. Si continúas, se borrará tu pedido anterior para
                agregar este.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <button wire:click="clearCart"
                    class="w-full rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg transition-transform active:scale-95">
                    Nuevo pedido
                </button>

                <button x-on:click="open = false"
                    class="w-full py-2 text-sm font-bold text-gray-400 active:text-gray-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    <livewire:components.cart-bar />
</div>
