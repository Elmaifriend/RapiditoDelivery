<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Enums\BusinessDecisionStatus;
use App\Enums\OrderLifecycleStatus;
use App\Services\OrderDispatchService;

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
            'lifecycle_status' => OrderLifecycleStatus::CONFIRMED,
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

    /**
     * Dispara el servicio de asignación/despacho de repartidor.
     */
    public function llamarRepartidor($orderId, OrderDispatchService $dispatchService)
    {
        $pedido = Order::where('business_id', $this->businessId)->findOrFail($orderId);
        
        // Ejecutamos la lógica de negocio para buscar y asignar un repartidor disponible
        $dispatchService->dispatchOrder($pedido);

        session()->flash('message', "Solicitud de repartidor enviada para el pedido #{$pedido->id}.");
    }

    public function render()
    {
        return view('livewire.business-orders-dashboard', [
            'pedidosNuevos' => $this->orders,
            'pedidosAceptados' => $this->acceptedOrders,
        ])->layout('layouts.app'); 
    }
}