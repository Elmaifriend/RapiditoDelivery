<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;

new #[Title('Cart')] class extends Component {
    #[Computed]
    public function carts()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        return Cart::with(['business', 'items'])
            ->where('status', 'active')
            ->where(function ($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->get();
    }

    public function incrementItem(CartItem $item)
    {
        $item->incrementQuantity(1);
        $item->cart->recalculateTotals();
    }

    public function decrementItem(CartItem $item)
    {
        if ($item->quantity > 1) {
            $item->quantity--;
            $item->recalculateSubtotal();
            $item->cart->recalculateTotals();
        } else {
            $cart = $item->cart;
            $item->delete();

            if ($cart->items()->count() === 0) {
                $cart->delete();
            } else {
                $cart->recalculateTotals();
            }
        }
    }

    #[Computed]
    public function totals()
    {
        return [
            'subtotal' => $this->carts()->sum('subtotal'),
            'delivery' => $this->carts()->sum('delivery_fee'),
            'total' => $this->carts()->sum('total'),
        ];
    }
};
?>
<div class="flex flex-col gap-4 p-4">
    @forelse($this->carts as $index => $cart)
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white">
            <div class="flex items-center justify-between bg-gray-900 p-3 px-5 text-white">
                <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                    Pedido {{ $index + 1 }} de {{ $this->carts->count() }}
                </span>
                <div class="flex items-center gap-1.5 text-xs font-medium text-gray-300">
                    <span>{{ $cart->business->delivery_time }} min</span>
                    <i class="fas fa-motorcycle"></i>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4 border-b border-gray-50 pb-4">
                    <h3 class="text-lg font-bold leading-tight text-gray-800">{{ $cart->business->name }}</h3>
                </div>

                <div class="mb-4 space-y-4">
                    @foreach ($cart->items as $item)
                        <div
                            class="group relative flex items-center justify-between gap-4 rounded-2xl border border-transparent bg-white transition-all hover:border-gray-100">

                            <div class="flex items-center gap-3">
                                <div class="h-14 w-14 flex-none overflow-hidden rounded-xl bg-gray-50">
                                    <img src="{{ $item->product_image_url_snapshot ? Storage::temporaryUrl($item->product_image_url_snapshot, now()->addMinutes(10)) : 'https://placehold.co/100x100' }}"
                                        class="h-full w-full object-cover">
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-gray-800"
                                        title="{{ $item->product_name_snapshot }}">
                                        {{ $item->product_name_snapshot }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        ${{ number_format($item->price_snapshot, 2) }} c/u
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 p-1">
                                    <button wire:click="decrementItem({{ $item->id }})" wire:loading.attr="disabled"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-xs font-bold text-gray-500 shadow-sm transition-all active:scale-90 disabled:opacity-50">
                                        @if ($item->quantity > 1)
                                            -
                                        @else
                                            <i class="fas fa-trash text-[10px] text-red-400"></i>
                                        @endif
                                    </button>

                                    <span class="w-6 text-center text-xs font-black text-gray-700"
                                        wire:target="decrementItem({{ $item->id }}), incrementItem({{ $item->id }})">
                                        {{ $item->quantity }}
                                    </span>

                                    <button wire:click="incrementItem({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-red-500 text-xs font-bold text-white shadow-sm transition-all active:scale-90 disabled:opacity-50">
                                        +
                                    </button>
                                </div>

                                <span class="text-sm font-black text-gray-900">
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

    @if ($this->carts->isNotEmpty())
        <div class="rounded-4xl bg-white p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Resumen de Pagos</h3>
            <div class="mb-2 flex justify-between px-2.5 text-sm text-gray-600">
                <span>Comida ({{ $this->carts->count() }} Rest.)</span>
                <span>${{ number_format($this->totals['subtotal'], 2) }}</span>
            </div>
            <div
                class="mb-2 flex justify-between rounded-xl border border-red-100 bg-red-50 p-2.5 text-sm font-bold text-red-600">
                <span>Envíos</span>
                <span>${{ number_format($this->totals['delivery'], 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-4 text-2xl font-bold text-gray-900">
                <span>Total Final</span>
                <span>${{ number_format($this->totals['total'], 2) }} MXN</span>
            </div>
        </div>

        <a href="{{ route('location', ['mode' => 'checkout']) }}" wire:navigate
            class="w-full rounded-2xl bg-red-500 px-6 py-4 text-center font-bold text-white transition-all active:scale-90">
            Continuar
        </a>
    @endif
</div>
