<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Enums\BusinessDecisionStatus;
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
     * PESTAÑA: POR ACEPTAR (NUEVOS)
     * Filtra pedidos pendientes de decisión por parte del restaurante.
     */
    public function getOrdersProperty()
    {
        return Order::where('business_id', $this->businessId)
            ->where('business_decision_status', BusinessDecisionStatus::PENDING)
            ->latest()
            ->get();
    }

    /**
     * PESTAÑA: EN COCINA (PREPARACIÓN)
     * Filtra los que ya fueron confirmados y están en cocina.
     */
    public function getAcceptedOrdersProperty()
    {
        return Order::where('business_id', $this->businessId)
            ->where('lifecycle_status', OrderLifecycleStatus::CONFIRMED)
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
            'business_decision_status' => BusinessDecisionStatus::ACCEPTED,
            'lifecycle_status' => OrderLifecycleStatus::CONFIRMED, // Al aceptar, pasa a cocina (CONFIRMED)
        ]);
        
        session()->flash('message', "Order #{$pedido->id} accepted.");
    }

    public function rechazarPedido($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'business_decision_status' => BusinessDecisionStatus::REJECTED,
            'lifecycle_status' => OrderLifecycleStatus::CANCELLED
        ]);
    }

    public function llamarRepartidor($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'delivery_status' => DeliveryStatus::WAITING_DRIVER
        ]);
    }

    public function completarPedido($orderId)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        $pedido->update([
            'lifecycle_status' => OrderLifecycleStatus::COMPLETED // Usamos COMPLETED que sí existe en tu Enum
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