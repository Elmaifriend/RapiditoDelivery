<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Search Product')] class extends Component {
    //
};
?>

<div class="flex flex-col gap-4 p-4">
    {{-- Very little is needed to make a happy life. - Marcus Aurelius --}}
    <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4">
        <i class="bxf bx-search text-lg text-red-400"></i>
        <input type="text" class="w-full text-sm font-medium text-gray-400" placeholder="¿Qué se te antoja hoy?">
    </div>

    <div class="flex w-full flex-col gap-2">
        <h2 class="font-bold">Recientes</h2>

        <div class="flex flex-wrap gap-4">
            <div
                class="flex select-none items-center gap-1 rounded-xl border border-transparent bg-gray-50 px-3 py-2 text-sm font-medium text-gray-400 transition-all active:scale-90 active:border-red-500 active:bg-red-50 active:text-red-500">
                <i class="bxf bx-clock"></i>
                <span>KFC</span>
            </div>
            <div
                class="flex select-none items-center gap-1 rounded-xl border border-transparent bg-gray-50 px-3 py-2 text-sm font-medium text-gray-400 transition-all active:scale-90 active:border-red-500 active:bg-red-50 active:text-red-500">
                <i class="bxf bx-clock"></i>
                <span>Pizza</span>
            </div>
            <div
                class="flex select-none items-center gap-1 rounded-xl border border-transparent bg-gray-50 px-3 py-2 text-sm font-medium text-gray-400 transition-all active:scale-90 active:border-red-500 active:bg-red-50 active:text-red-500">
                <i class="bxf bx-clock"></i>
                <span>Hamburguesas</span>
            </div>
            <div
                class="flex select-none items-center gap-1 rounded-xl border border-transparent bg-gray-50 px-3 py-2 text-sm font-medium text-gray-400 transition-all active:scale-90 active:border-red-500 active:bg-red-50 active:text-red-500">
                <i class="bxf bx-clock"></i>
                <span>Farmacia</span>
            </div>
        </div>
    </div>

    <div class="flex w-full flex-col gap-2">
        <h2 class="font-bold">Top Categorías</h2>

        <div class="grid grid-cols-2 grid-rows-2 gap-4">
            <div
                class="rounded-4xl relative flex h-28 flex-col justify-center overflow-hidden border border-orange-200 bg-orange-100 p-5 transition-transform active:scale-90">
                <span class="relative z-10 text-lg font-bold leading-tight text-orange-900">Comida<br>Rápida</span>
                <i class="bxf bx-burger absolute -bottom-3 -right-3 text-7xl text-orange-300"></i>
            </div>
            <div
                class="rounded-4xl relative flex h-28 flex-col justify-center overflow-hidden border border-green-200 bg-green-100 p-5 transition-transform active:scale-90">
                <span class="relative z-10 text-lg font-bold leading-tight text-green-900">Saludable<br>& Fit</span>
                <i class="bxf bx-carrot absolute -bottom-3 -right-3 text-7xl text-green-300"></i>
            </div>
            <div
                class="rounded-4xl relative flex h-28 flex-col justify-center overflow-hidden border border-blue-200 bg-blue-100 p-5 transition-transform active:scale-90">
                <span class="relative z-10 text-lg font-bold leading-tight text-blue-900">Bebidas<br>& Licores</span>
                <i class="bxf bx-wine absolute -bottom-3 -right-3 text-7xl text-blue-300"></i>
            </div>
            <div
                class="rounded-4xl relative flex h-28 flex-col justify-center overflow-hidden border border-pink-200 bg-pink-100 p-5 transition-transform active:scale-90">
                <span class="relative z-10 text-lg font-bold leading-tight text-pink-900">Postres<br>& Dulces</span>
                <i class="bxf bx-icecream absolute -bottom-3 -right-3 text-7xl text-pink-300"></i>
            </div>
        </div>
    </div>

    <div class="flex w-full flex-col gap-2">
        <h2 class="font-bold">Tendencias hoy 🔥</h2>

        <div class="divide-y divide-gray-200">
            <div class="flex items-center gap-8 py-3">
                <span class="text-2xl font-bold text-gray-400">1</span>
                <div class="flex-1">
                    <p class="font-bold">Little Caesars 👑</p>
                    <p class="text-xs text-gray-500">Pizza • 25-35 min</p>
                </div>
                <span
                    class="self-start rounded-xl border border-red-200 bg-red-100 px-2 py-1 text-xs font-bold text-red-500">
                    Promo
                </span>
            </div>

            <div class="flex items-center gap-8 py-3">
                <span class="text-2xl font-bold text-gray-400">2</span>
                <div class="flex-1">
                    <p class="font-bold">Burger Lab 🥈</p>
                    <p class="text-xs text-gray-500">Hamburguesas • 20-30 min</p>
                </div>
            </div>

            <div class="flex items-center gap-8 py-3">
                <span class="text-2xl font-bold text-gray-400">3</span>
                <div class="flex-1">
                    <p class="font-bold">Sushi Itto 🥉</p>
                    <p class="text-xs text-gray-500">Sushi • 40-50 min</p>
                </div>
            </div>

            <div class="flex items-center gap-8 py-3">
                <span class="text-2xl font-bold text-gray-400">4</span>
                <div class="flex-1">
                    <p class="font-bold">Green Bowl</p>
                    <p class="text-xs text-gray-500">Saludable • 15-25 min</p>
                </div>
                <span
                    class="self-start rounded-xl border border-red-200 bg-red-100 px-2 py-1 text-xs font-bold text-red-500">
                    Promo
                </span>
            </div>

        </div>
    </div>
</div>
