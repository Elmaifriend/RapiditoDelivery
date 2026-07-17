<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Order;

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
            'dropoffLocations'
        ]);
    }

    public function render()
    {
        return view('livewire.checkout-success');
    }
}