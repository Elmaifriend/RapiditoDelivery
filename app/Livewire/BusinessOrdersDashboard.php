<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Enums\RestaurantDecisionStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\DeliveryStatus;

class BusinessOrdersDashboard extends Component
{
    public int $businessId;
    public string $activeTab = 'nuevos';

    public function mount($businessId)
    {
        $this->businessId = (int) $businessId; 
    }

    /**
     * PESTAÑA: POR ACEPTAR
     * Filtra pedidos pendientes de decisión por parte del restaurante.
     */
    public function getOrdersProperty()
    {
        return Order::where('business_id', $this->businessId)
            ->where('business_decision_status', RestaurantDecisionStatus::PENDING->value)
            ->latest()
            ->get();
    }

    /**
     * PESTAÑA: EN COCINA
     * Filtra los que ya fueron aceptados pero siguen en el ciclo de preparación o listos.
     */
    public function getAcceptedOrdersProperty()
    {
        return Order::where('business_id', $this->businessId)
            ->whereIn('lifecycle_status', [
                OrderLifecycleStatus::ACCEPTED_BY_RESTAURANT->value,
                OrderLifecycleStatus::IN_PREPARATION->value,
                OrderLifecycleStatus::READY->value
            ])
            ->latest()
            ->get();
    }

    public function changeTab($tab)
    {
        if (in_array($tab, ['nuevos', 'preparacion'])) {
            $this->activeTab = $tab;
        }
    }

    public function aceptarPedido($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'business_decision_status' => RestaurantDecisionStatus::ACCEPTED->value,
            'lifecycle_status' => OrderLifecycleStatus::ACCEPTED_BY_RESTAURANT->value
        ]);
        
        session()->flash('message', "Order #{$pedido->id} accepted.");
    }

    public function rechazarPedido($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'business_decision_status' => RestaurantDecisionStatus::REJECTED->value,
            'lifecycle_status' => OrderLifecycleStatus::CANCELLED->value
        ]);
    }

    public function llamarRepartidor($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'lifecycle_status' => OrderLifecycleStatus::IN_PREPARATION->value,
            'delivery_status' => DeliveryStatus::WAITING_DRIVER->value
            // Nota: Si no tienes 'driver_called_at' en tu migración, puedes usar 
            // timestamps del sistema o manejarlo mediante el delivery_status anterior.
        ]);
    }

    public function completarPedido($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'lifecycle_status' => OrderLifecycleStatus::READY->value
        ]);
    }

    public function render()
    {
        return view('livewire.business-orders-dashboard', [
            'pedidosNuevos' => $this->orders,
            'pedidosAceptados' => $this->acceptedOrders,
        ])->layout('layouts.app'); 
    }
}