<?php

namespace App\Livewire\Pages;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeliveryAddress;
use App\Services\DeliveryFeeCalculatorService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart')]
class CartPage extends Component
{
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
        return DeliveryAddress::where('guest_token', request()->cookie('guest_token'))->first();
    }

    public function getEstimatedDeliveryFee(Cart $cart): float
    {
        $business = $cart->business;
        $address = $this->defaultAddress;

        if (! $business || ! $business->lat || ! $business->lng) {
            return (float) ($cart->delivery_fee ?? 0);
        }

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
        $delivery = $this->carts->reduce(fn ($carry, $cart) => $carry + $this->getEstimatedDeliveryFee($cart), 0);

        return [
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
        ];
    }

    public function render()
    {
        return view('livewire.pages.cart');
    }
}
