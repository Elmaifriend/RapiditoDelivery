<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeliveryAddress;
use App\Services\DeliveryFeeCalculatorService;

new #[Title('Cart')] class extends Component {
    
    #[Computed]
    public function carts()
    {
        return Cart::with(['business', 'items'])
            ->where('status', 'active')
            ->where(fn ($query) => auth()->check() 
                ? $query->where('user_id', auth()->id()) 
                : $query->where('guest_token', request()->cookie('guest_token'))
            )->get();
    }

    #[Computed]
    public function defaultAddress()
    {

        //Comente este fragmento porque causa conflictos, aun no esta bien definido como manejar los auth
        //Mientras hacia pruebas, inicie sesion en el panel de filament, y me lo tomo como que inicies sesion en la app
        //y eso causo que nunca encontrara ninguna address, ya que hasta ahora, el sistema esta pensado solo para guardar 
        //las direccion con un guest token

        
        /* $userId = auth()->id();

        if ($userId) {
            return DeliveryAddress::where('user_id', $userId)->where('is_default', true)->first()
                ?? DeliveryAddress::where('user_id', $userId)->latest('last_used_at')->first();
        } */

        return DeliveryAddress::where('guest_token', request()->cookie('guest_token'))->first();
    }

    public function getEstimatedDeliveryFee(Cart $cart): float
    {
        $business = $cart->business;
        $address = $this->defaultAddress; // Usamos el caché del #[Computed]

        // Si el restaurante no tiene mapa configurado, no podemos hacer mucho
        if (!$business || !$business->lat || !$business->lng) {
            return (float) ($cart->delivery_fee ?? 0);
        }

        // Le pasamos todo al Service. Usamos el nullsafe operator (?->) por si el address no existe
        $fee = app(DeliveryFeeCalculatorService::class)->calculate(
            $business->lat, 
            $business->lng, 
            $address?->lat, 
            $address?->lng
        );

        return $fee ?? (float) ($cart->delivery_fee ?? 0);
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
            return; 
        } 
        
        $this->removeEmptyCartOrItem($item);
    }

    private function removeEmptyCartOrItem(CartItem $item)
    {
        $cart = $item->cart;
        $item->delete();

        $cart->items()->count() === 0 
            ? $cart->delete() 
            : $cart->recalculateTotals();
    }

    #[Computed]
    public function totals()
    {
        $subtotal = $this->carts->sum('subtotal');
        $delivery = $this->carts->reduce(fn($carry, $cart) => $carry + $this->getEstimatedDeliveryFee($cart), 0);

        return [
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total'    => $subtotal + $delivery,
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
                        <span class="flex items-center gap-1">
                            Envío Estimado
                            <i class="fas fa-info-circle text-gray-400" title="Se calculará exacto al seleccionar dirección"></i>
                        </span>
                        <span>${{ number_format($this->getEstimatedDeliveryFee($cart), 2) }}</span>
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
            <h3 class="mb-4 text-lg font-bold text-gray-800">Resumen Preliminar</h3>
            <div class="mb-2 flex justify-between px-2.5 text-sm text-gray-600">
                <span>Comida ({{ $this->carts->count() }} Rest.)</span>
                <span>${{ number_format($this->totals['subtotal'], 2) }}</span>
            </div>
            <div
                class="mb-2 flex justify-between rounded-xl border border-blue-100 bg-blue-50 p-2.5 text-sm font-bold text-blue-600">
                <span>Envío Estimado</span>
                <span>${{ number_format($this->totals['delivery'], 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-4 text-2xl font-bold text-gray-900">
                <span>Total Aprox.</span>
                <span>${{ number_format($this->totals['total'], 2) }} MXN</span>
            </div>
            <p class="mt-3 text-center text-[10px] text-gray-400">El total exacto se calculará en el siguiente paso.</p>
        </div>

        <a href="{{ route('location', ['mode' => 'checkout']) }}" wire:navigate
            class="w-full rounded-2xl bg-red-500 px-6 py-4 text-center font-bold text-white transition-all active:scale-90">
            Seleccionar ubicacion
        </a>
    @endif
</div>