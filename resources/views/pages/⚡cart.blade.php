<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;

new #[Title('Cart')] class extends Component {

    #[Computed]
    public function carts()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        return Cart::with(['restaurant', 'items'])
            ->where('status', 'active')
            ->where(function($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->get();
    }

    #[Computed]
    public function totals()
    {
        return [
            'subtotal' => $this->carts()->sum('subtotal'),
            'delivery' => $this->carts()->sum('delivery_fee'),
            'total' => $this->carts()->sum('total')
        ];
    }
};
?>

<div class="flex flex-col gap-4 p-4">
    @forelse($this->carts as $index => $cart)
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
            {{-- Encabezado del Pedido --}}
            <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
                <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                    Pedido {{ $index + 1 }} de {{ $this->carts->count() }}
                </span>
                <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                    <span>{{ $cart->restaurant->delivery_time }} min</span>
                    <i class="fas fa-motorcycle"></i>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold leading-tight text-gray-800">{{ $cart->restaurant->name }}</h3>
                </div>

                {{-- Items del Carrito --}}
                <div class="mb-4 space-y-4">
                    @foreach($cart->items as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-xs font-bold text-gray-600">
                                    {{ $item->quantity }}x
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">{{ $item->product_name_snapshot }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-800">${{ number_format($item->price_snapshot, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Totals por Restaurante --}}
                <div class="space-y-1.5 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal comida</span>
                        <span>${{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800">
                        <span class="flex items-center gap-1">Envío</span>
                        <span>${{ number_format($cart->delivery_fee, 2) }}</span>
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

    {{-- Resumen de Pagos Global --}}
    @if($this->carts->isNotEmpty())
        <div class="rounded-4xl bg-white p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Resumen de Pagos</h3>
            <div class="mb-2 flex justify-between px-2.5 text-sm text-gray-600">
                <span>Comida ({{ $this->carts->count() }} Rest.)</span>
                <span>${{ number_format($this->totals['subtotal'], 2) }}</span>
            </div>
            <div class="mb-2 flex justify-between rounded-xl border border-red-100 bg-red-50 p-2.5 text-sm font-bold text-red-600">
                <span>Envíos</span>
                <span>${{ number_format($this->totals['delivery'], 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-4 text-2xl font-bold text-gray-900">
                <span>Total Final</span>
                <span>${{ number_format($this->totals['total'], 2) }} MXN</span>
            </div>
        </div>

        <a href="{{ route('checkout') }}" wire:navigate
            class="w-full rounded-2xl bg-red-500 px-6 py-4 text-center font-bold text-white transition-all active:scale-90">
            Pagar Pedidos
        </a>
    @endif
</div>
