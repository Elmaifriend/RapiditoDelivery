@extends('layouts.page')

@section('content')
    <div class="space-y-4 border-b border-gray-100 bg-white px-4 pb-8 pt-12 text-center">
        <div class="mx-auto flex max-w-md flex-col items-center space-y-4">
            <div class="flex h-16 w-16 animate-bounce items-center justify-center rounded-full bg-green-50 text-green-500">
                <i class="bxf bx-check text-4xl"></i>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">¡Pedido Realizado!</h1>
                <p class="flex items-center justify-center gap-1.5 text-sm font-bold text-green-600">
                    <i class="bxf bxl-whatsapp text-lg"></i>
                    En seguida nos comunicamos por WA
                </p>
            </div>

            <p class="max-w-xs text-xs font-semibold leading-relaxed text-gray-500">
                Hemos recibido tu orden correctamente. Nuestro equipo ya está coordinando los detalles para que tu entrega
                sea lo más rápida posible.
            </p>

            <div class="inline-block rounded-full bg-gray-100 px-4 py-1.5">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-700">
                    Orden #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
    </div>

    {{-- 

        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="">
                   

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>
        
    --}}

    @if ($order->business)
        <x-ui.page-section>
            <x-ui.card padding="py-6 px-2">
                <x-ui.page-section title="Datos de la tienda">

                    <div class="space-y-1">
                        <p class="text-sm font-bold text-gray-800">{{ $order->business->name }}</p>
                        @if ($order->business->address)
                            <p class="text-xs font-semibold text-gray-500">{{ $order->business->address }}</p>
                        @endif
                        @if ($order->business->phone)
                            <p class="text-xs font-semibold text-gray-500">Tel: {{ $order->business->phone }}</p>
                        @endif
                    </div>

                </x-ui.page-section>
            </x-ui.card>
        </x-ui.page-section>
    @endif

    <x-ui.page-section>
        <x-ui.card padding="py-6 px-2">
            <x-ui.page-section title="Datos del cliente">

                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-800">{{ $order->customer_name }}</p>
                    <p class="text-xs font-semibold text-gray-500">{{ $order->customer_phone }}</p>
                </div>

            </x-ui.page-section>
        </x-ui.card>
    </x-ui.page-section>

    <x-ui.page-section>
        <x-ui.card padding="py-6 px-2">
            <x-ui.page-section title="Dirección de entrega">

                <div class="space-y-3">
                    @php
                        $dropoff = $order->dropoffLocations->first();
                    @endphp

                    <p class="text-sm font-bold leading-snug text-gray-800">
                        {{ $dropoff?->formatted_address ?? ($dropoff?->address_line ?? 'Dirección registrada') }}
                    </p>

                    @if ($dropoff?->reference)
                        <div class="rounded-xl border border-amber-100/50 bg-amber-50/70 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Referencia aportada:
                            </p>
                            <p class="mt-0.5 text-xs font-semibold text-amber-900">{{ $dropoff->reference }}</p>
                        </div>
                    @endif

                    @if ($order->special_instructions)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Instrucciones
                                especiales:</p>
                            <p class="mt-0.5 text-xs font-semibold text-gray-700">{{ $order->special_instructions }}</p>
                        </div>
                    @endif
                </div>

            </x-ui.page-section>
        </x-ui.card>
    </x-ui.page-section>

    <x-ui.page-section>
        <div class="shadow-xs flex items-center justify-between rounded-2xl border border-gray-100/50 bg-white p-4">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-50 text-gray-700">
                    @if ($order->payment_method->value === 'card')
                        <i class="bxf bx-credit-card text-base"></i>
                    @else
                        <i class="bxf bx-dollar text-base"></i>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Método de pago</h3>
                    <p class="mt-0.5 text-xs font-semibold text-gray-500">
                        {{ $order->payment_method->value === 'card' ? 'Pago con Tarjeta (Terminal)' : 'Pago en Efectivo' }}
                    </p>
                </div>
            </div>
            <x-ui.badge color="amber" pulse>
                <span>Pendiente de Pago</span>
            </x-ui.badge>
        </div>
    </x-ui.page-section>

    <x-ui.page-section>
        <x-ui.card padding="py-6 px-2">
            <x-ui.page-section title="Resumen de productos">

                <div class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <x-ui.product-item
                            class="py-3"
                            :quantity="$item->quantity"
                            :name="$item->product_name_snapshot"
                            :price="$item->subtotal ?? $item->price * $item->quantity"
                            :notes="$item->product_description_snapshot"
                        />
                    @endforeach
                </div>

                {{-- Desglose totalizador --}}
                <div class="-mx-4 -mb-4 space-y-2 rounded-b-2xl border-t border-gray-100 bg-gray-50/50 p-4 pt-4">
                    <div class="flex justify-between text-xs font-bold text-gray-500">
                        <span>Subtotal</span>
                        <span class="font-mono text-gray-700">${{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xs font-bold text-gray-500">
                        <span>Envío a domicilio</span>
                        <span class="font-mono text-gray-700">${{ number_format($order->delivery_fee, 2) }}</span>
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-gray-100 pt-3 text-sm font-bold text-gray-900">
                        <span>Total de la Orden</span>
                        <span
                            class="font-mono text-lg font-bold text-red-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

            </x-ui.page-section>
        </x-ui.card>
    </x-ui.page-section>

    <x-ui.page-section>
        <x-ui.button
            class="w-full"
            href="/"
            variant="dark"
        >
            <i class="bxf bx-home-alt text-base"></i>
            Volver a la Tienda
        </x-ui.button>
    </x-ui.page-section>
@endsection
