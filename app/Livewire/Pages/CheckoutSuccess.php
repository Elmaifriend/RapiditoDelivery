<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('¡Pedido Completado!')]
class CheckoutSuccess extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Cargamos todas las relaciones necesarias para el desglose estilo recibo
        $this->order = $order->load([
            'items.product',
            'business',
            'dropoffLocations',
        ]);
    }

    public function render()
    {
        return view('livewire.pages.checkout-success');
    }
}
