<?php

namespace App\Livewire\Pages;

use App\Enums\BusinessDecisionStatus;
use App\Enums\OrderLifecycleStatus;
use App\Models\Order;
use App\Services\OrderDispatchService;
use App\Services\WhatsAppNotifierService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BusinessOrdersDashboard extends Component
{
    public int $businessId;

    public string $activeTab = 'nuevos';

    public function mount(int $businessId): void
    {
        $this->businessId = $businessId;
    }

    /**
     * TAB: NUEVOS PEDIDOS (PENDIENTES DE ACEPTAR)
     */
    #[Computed]
    public function pedidosNuevos()
    {
        return Order::with('items')
            ->where('business_id', $this->businessId)
            ->where('business_decision_status', BusinessDecisionStatus::PENDING)
            ->latest()
            ->get();
    }

    /**
     * TAB: EN COCINA (CONFIRMADOS Y EN PREPARACIÓN)
     */
    #[Computed]
    public function pedidosAceptados()
    {
        return Order::with(['items', 'driver'])
            ->where('business_id', $this->businessId)
            ->where('lifecycle_status', OrderLifecycleStatus::CONFIRMED)
            ->whereNotIn('business_decision_status', [
                BusinessDecisionStatus::PENDING,
                BusinessDecisionStatus::REJECTED,
            ])
            ->orderByRaw("CASE WHEN driver_id IS NULL THEN 0 ELSE 1 END")
            ->latest()
            ->get();
    }

    public function changeTab(string $tab): void
    {
        if (in_array($tab, ['nuevos', 'preparacion'])) {
            $this->activeTab = $tab;
        }
    }

    public function aceptarPedido(int $orderId, WhatsAppNotifierService $notifier): void
    {
        $order = Order::where('business_id', $this->businessId)->findOrFail($orderId);

        $order->update([
            'business_decision_status' => BusinessDecisionStatus::ACCEPTED,
            'lifecycle_status' => OrderLifecycleStatus::CONFIRMED,
        ]);

        // Notificar al cliente vía WhatsApp
        $notifier->notifyCustomerOrderAccepted($order);

        session()->flash('message', "Pedido #{$order->id} aceptado.");
    }

    public function rechazarPedido(int $orderId, WhatsAppNotifierService $notifier): void
    {
        $order = Order::where('business_id', $this->businessId)->findOrFail($orderId);

        $order->update([
            'business_decision_status' => BusinessDecisionStatus::REJECTED,
            'lifecycle_status' => OrderLifecycleStatus::CANCELLED,
        ]);

        // Notificar al cliente vía WhatsApp el rechazo del pedido
        $notifier->notifyCustomerOrderRejected($order);

        session()->flash('message', "Pedido #{$order->id} rechazado.");
    }

    /**
     * Solicita la asignación de repartidor a través del servicio de despacho.
     */
    public function llamarRepartidor(int $orderId, OrderDispatchService $dispatchService): void
    {
        $order = Order::where('business_id', $this->businessId)->findOrFail($orderId);

        $dispatchService->dispatchOrder($order);

        session()->flash('message', "Solicitud de repartidor enviada para el pedido #{$order->id}.");
    }

    public function render()
    {
        return view('livewire.pages.business-orders-dashboard', [
            'pedidosNuevos' => $this->pedidosNuevos,
            'pedidosAceptados' => $this->pedidosAceptados,
        ]);
    }
}