<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Restaurant;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

new #[Title('Restaurant')] class extends Component
{
    public Restaurant $restaurant;

    public ?Product $selectedProduct = null;
    public int $quantity = 1;
    public bool $showModal = false;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant->load([
            'category',
            'productCategories.products'
        ]);
    }

    public function openProductModal(Product $product)
    {
        $this->selectedProduct = $product;
        $this->quantity = 1;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function increment() { $this->quantity++; }
    public function decrement() { if($this->quantity > 1) $this->quantity--; }

    public function addToCart()
    {
        if (!$this->selectedProduct) return;

        $guestToken = request()->cookie('guest_token');

        $userId = auth()->id();

        $cart = Cart::firstOrCreate(
            [
                'user_id' => $userId,
                'guest_token' => $guestToken,
                'status' => 'active',
                'restaurant_id' => $this->restaurant->id,
            ],
            [
                'expires_at' => now()->addDays(3),
            ]
        );

        $cartItem = $cart->items()->where('product_id', $this->selectedProduct->id)->first();

        if ($cartItem) {
            $cartItem->incrementQuantity($this->quantity);
        } else {
            $cart->items()->create([
                'product_id' => $this->selectedProduct->id,
                'product_name_snapshot' => $this->selectedProduct->name,
                'product_description_snapshot' => $this->selectedProduct->description,
                'product_image_url_snapshot' => $this->selectedProduct->image_path,
                'price_snapshot' => $this->selectedProduct->price,
                'quantity' => $this->quantity,
                'subtotal' => $this->selectedProduct->price * $this->quantity,
            ]);
        }

        $cart->recalculateTotals();

        $this->closeModal();
        $this->dispatch('cart-updated');
    }
};

?>

<div class="min-h-screen bg-gray-50 pb-28">

    {{-- HEADER --}}
    <div class="relative h-52 w-full overflow-hidden bg-gray-200">

        <img src="{{ $restaurant->banner_path ? Storage::url($restaurant->banner_path, now()->addMinutes(10)) : 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop' }}"
            class="h-full w-full object-cover">

        <div class="absolute left-0 right-0 top-0 flex items-center justify-between p-4">

            <button onclick="history.back()"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
                ←
            </button>

            <button class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
                ♡
            </button>

        </div>
    </div>


    {{-- INFO RESTAURANTE --}}
    <div class="bg-white px-5 pb-5 pt-4">

        <h1 class="text-2xl font-bold text-gray-800">
            {{ $restaurant->name }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            {{ $restaurant->category?->name }} • 25-35 min
        </p>

    </div>


    {{-- CATEGORÍAS --}}
    <div class="sticky top-0 z-10 bg-gray-50">

        <div class="no-scrollbar flex gap-3 overflow-x-auto px-4 py-4">

            @foreach($restaurant->productCategories as $category)

            <a href="#category-{{ $category->id }}"
                class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 active:scale-90 transition-transform">

                {{ $category->name }}

            </a>

            @endforeach

        </div>

    </div>


    {{-- PRODUCTOS --}}
    <div class="space-y-8 px-4 pt-4">

        @foreach($restaurant->productCategories as $category)

        <div id="category-{{ $category->id }}">

            <h2 class="mb-3 text-lg font-bold text-gray-800">
                {{ $category->name }}
            </h2>

            <div class="space-y-4">

                @foreach($category->products->where('is_active', true)->where('is_available', true) as $product)

                <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3">

                    {{-- INFO PRODUCTO --}}
                    <div class="flex-1 pr-4">

                        <h3 class="text-sm font-bold text-gray-800">
                            {{ $product->name }}
                        </h3>

                        @if($product->description)
                        <p class="mt-1 text-xs text-gray-400 line-clamp-2">
                            {{ $product->description }}
                        </p>
                        @endif

                        <span class="mt-2 block font-bold text-gray-900">
                            ${{ number_format($product->price, 2) }}
                        </span>

                    </div>


                    {{-- IMAGEN --}}
                    <div class="flex flex-col items-center gap-2">

                        <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-gray-200">

                            @if($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}" class="h-full w-full object-cover">
                            @else
                            <img src="https://placehold.co/150x150" class="h-full w-full object-cover">
                            @endif

                        </div>

                        {{-- BOTÓN --}}
                        <button wire:click="openProductModal({{ $product->id }})"
                            class="rounded-lg border border-red-200 px-4 py-1 text-xs font-bold text-red-500 active:scale-90 transition-transform">
                            Agregar
                        </button>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

        @endforeach

    </div>


    <div x-data="{ open: @entangle('showModal') }" x-show="open"
        class="fixed inset-0 z-50 flex items-end justify-center sm:items-center" x-cloak>

        {{-- Backdrop --}}
        <div x-show="open" x-transition.opacity x-on:click="open = false"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        {{-- Contenido del Modal --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 sm:scale-100"
            class="relative w-full max-w-lg overflow-hidden rounded-t-3xl bg-white p-6 pb-28 shadow-xl sm:rounded-3xl">

            @if($selectedProduct)
            <div class="relative h-64 w-full overflow-hidden rounded-2xl bg-gray-100">
                <img src="{{ $selectedProduct->image_path ? Storage::url($selectedProduct->image_path) : 'https://placehold.co/600x400' }}"
                    class="h-full w-full object-cover">
                <button x-on:click="open = false"
                    class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-md">✕</button>
            </div>

            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-800">{{ $selectedProduct->name }}</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $selectedProduct->description }}</p>
                <span class="mt-4 block text-lg font-black text-gray-900">${{ number_format($selectedProduct->price, 2)
                    }}</span>
            </div>

            <div class="mt-8 flex items-center justify-between">
                {{-- Selector de cantidad --}}
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 p-1">
                    <button wire:click="decrement"
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">-</button>
                    <span class="w-8 text-center font-bold text-gray-800">{{ $quantity }}</span>
                    <button wire:click="increment"
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 font-bold text-gray-600 active:bg-gray-200">+</button>
                </div>

                {{-- Botón confirmar --}}
                <button wire:click="addToCart"
                    class="flex-1 ml-4 rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg active:scale-95 transition-transform">
                    Agregar ${{ number_format($selectedProduct->price * $quantity, 2) }}
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
