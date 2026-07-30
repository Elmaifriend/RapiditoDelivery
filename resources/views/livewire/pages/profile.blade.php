@extends('layouts.page')

@section('content')
    @auth
        <x-ui.page-section>
            <div class="flex items-center gap-4 pb-2 pt-4">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border border-red-200 bg-red-100 text-2xl font-black text-red-500 shadow-sm">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="overflow-hidden">
                    <h2 class="truncate text-xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <p class="truncate text-sm font-semibold text-gray-400">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </x-ui.page-section>

        @if (session()->has('message'))
            <x-ui.page-section>
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 shadow-sm">
                    <i class="bxf bx-check-circle text-xl text-emerald-500"></i>
                    <span>{{ session('message') }}</span>
                </div>
            </x-ui.page-section>
        @endif

        <x-ui.page-section title="Mis Datos">
            <form wire:submit="updateProfile" class="flex flex-col gap-4">
                <x-ui.card class="space-y-4">
                    <x-ui.input 
                        label="Nombre completo" 
                        wire:model="name" 
                        placeholder="Tu nombre" 
                        :error="$errors->first('name')"
                    />
                    <x-ui.input 
                        label="Correo Electrónico" 
                        type="email"
                        wire:model="email" 
                        placeholder="correo@ejemplo.com" 
                        :error="$errors->first('email')"
                    />
                    <x-ui.input 
                        label="Teléfono" 
                        type="tel"
                        wire:model="phone" 
                        placeholder="1234567890" 
                        :error="$errors->first('phone')"
                    />
                </x-ui.card>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit" class="flex-1">
                        Guardar Cambios
                    </x-ui.button>
                    <x-ui.button variant="secondary" wire:click="logout" type="button" class="shrink-0">
                        Cerrar Sesión
                    </x-ui.button>
                </div>
            </form>
        </x-ui.page-section>
    @endauth

    <x-ui.page-section title="Opciones" class="{{ auth()->check() ? 'mt-2' : 'mt-4' }}">
        <div class="flex flex-col gap-3">
            <a 
                href="https://wa.me/5216647921114" 
                target="_blank" 
                class="flex cursor-pointer select-none items-center gap-4 rounded-3xl border border-gray-100 bg-white p-4 shadow-sm transition-transform active:scale-[0.98]"
            >
                <i class="bxl bx-whatsapp rounded-2xl bg-green-100 p-3 text-2xl text-green-500"></i>
                <p class="font-bold text-gray-800">Ayuda y Soporte</p>
            </a>
        </div>
    </x-ui.page-section>
@endsection
