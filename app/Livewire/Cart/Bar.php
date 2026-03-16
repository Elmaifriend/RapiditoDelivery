<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Cart;

class Bar extends Component
{
    protected $listeners = ['cart-updated' => '$refresh'];

    public function getCartProperty()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        return Cart::where('status', 'active')
            ->where(function($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->first();
    }

    public function render()
    {
        return view('livewire.cart.bar');
    }
}