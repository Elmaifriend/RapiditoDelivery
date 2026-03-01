<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile')] class extends Component {
    //
};
?>

<div class="flex flex-col gap-4 p-6">
    {{-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh --}}

    <div class="flex items-center gap-4">
        <div class="size-20 rounded-full bg-gray-300">
        </div>
        <div>
            <h2 class="text-lg font-bold">Juan Pérez</h2>
            <p class="text-sm text-gray-400">juan.demo@email.com</p>
        </div>
    </div>

    <div class="flex gap-2">
        <div class="flex-1 rounded-2xl border border-red-200 bg-red-100 p-4 text-center">
            <span class="block text-2xl font-bold text-red-500">12</span>
            <span class="text-xs font-medium text-red-500">Pedidos mes</span>
        </div>
        <div class="flex-1 rounded-2xl border border-green-200 bg-green-100 p-4 text-center">
            <span class="block text-2xl font-bold text-green-800">4.9</span>
            <span class="text-xs font-medium text-green-500">Calificación</span>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <h2 class="font-bold">Opciones</h2>

        <div class="flex flex-col gap-4">
            <div
                class="flex select-none items-center gap-4 rounded-2xl bg-white p-4 transition-transform active:scale-90">
                <i class="bxf bx-location rounded-2xl bg-red-200 p-3 text-2xl text-red-500"></i>
                <p class="font-bold">Mis Direcciones</p>
            </div>
            <div
                class="flex select-none items-center gap-4 rounded-2xl bg-white p-4 transition-transform active:scale-90">
                <i class="bxf bx-credit-card-alt rounded-2xl bg-blue-200 p-3 text-2xl text-blue-500"></i>
                <p class="font-bold">Métodos de Pago</p>
            </div>
            <div
                class="flex select-none items-center gap-4 rounded-2xl bg-white p-4 transition-transform active:scale-90">
                <i class="bxf bx-bell rounded-2xl bg-amber-200 p-3 text-2xl text-amber-500"></i>
                <p class="font-bold">Notificaciones</p>
            </div>
            <div
                class="flex select-none items-center gap-4 rounded-2xl bg-white p-4 transition-transform active:scale-90">
                <i class="bxf bx-help-circle rounded-2xl bg-gray-200 p-3 text-2xl text-gray-500"></i>
                <p class="font-bold">Ayuda y Soporte</p>
            </div>
        </div>
    </div>
</div>
