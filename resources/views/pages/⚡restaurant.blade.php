<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Restaurant;

new #[Title('Restaurant')] class extends Component 
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant->load([
            'category',
            'productCategories.products'
        ]);
    }
};

<div class="min-h-screen bg-gray-50 pb-28">

    {{-- HEADER --}}
    <div class="relative h-52 w-full overflow-hidden bg-gray-200">

        <img
            src="{{ $restaurant->banner_path ? Storage::url($restaurant->banner_path) : 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop' }}"
            class="h-full w-full object-cover">

        <div class="absolute left-0 right-0 top-0 flex items-center justify-between p-4">

            <button
                onclick="history.back()"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
                ←
            </button>

            <button
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
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

                <a
                    href="#category-{{ $category->id }}"
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
                                <img
                                    src="{{ Storage::url($product->image_path) }}"
                                    class="h-full w-full object-cover">
                            @else
                                <img
                                    src="https://placehold.co/150x150"
                                    class="h-full w-full object-cover">
                            @endif

                        </div>

                        {{-- BOTÓN --}}
                        <button
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


    {{-- CARRITO --}}
    <div class="fixed bottom-0 left-0 right-0 border-t bg-white p-4">

        <button
            class="w-full rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg">

            Ver carrito

        </button>

    </div>

</div>