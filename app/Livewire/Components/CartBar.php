<?php

namespace App\Livewire\Components;

use App\Models\Cart;
use Livewire\Component;

class CartBar extends Component
{
    protected $listeners = ['cart-updated' => '$refresh', 'cart-updated' => '$refresh'];

    public function getCartProperty()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        return Cart::where('status', 'active')
            ->where(function ($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->first();
    }

    // Acción para forzar la revisión de ubicación antes del checkout
    public function goToLocation()
    {
        if (! $this->cart || $this->cart->items->count() === 0) {
            return;
        }

        // Lo mandamos al mapa obligatoriamente
        $this->redirect('/location?mode=checkout', navigate: true);
    }

    public function render()
    {
        return view('livewire.components.cart-bar');
    }
}
