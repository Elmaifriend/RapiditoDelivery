<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Driver;
use App\Enums\DeliveryStatus;
use App\Services\DriverAssignmentService;

class DriverTasksManager extends Component
{
    public Driver $driver;
    public ?Order $currentOrder = null;

    // Campos de reporte / supervisión
    public bool $allItemsCorrect = true;
    public string $driverNotes = '';

    // Campos de confirmación de entrega
    public string $paymentOutcome = 'paid_correctly';
    public string $incidentNotes = '';

    public function mount(Driver $driver): void
    {
        $this->driver = $driver;
        $this->loadActiveOrder();
    }

    public function loadActiveOrder(): void
    {
        if (!$this->driver) {
            $this->currentOrder = null;
            return;
        }

        // Buscar orden activa asignada al repartidor
        $this->currentOrder = Order::where('driver_id', $this->driver->id)
            ->whereIn('delivery_status', [
                DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT,
                DeliveryStatus::PICKED_UP,
                DeliveryStatus::ON_THE_WAY,
            ])
            ->with(['business', 'dropoffLocations', 'items.product'])
            ->first();
    }

    /**
     * Paso 1: El repartidor confirma recogida en el restaurante.
     */
    public function markAsPickedUp(DriverAssignmentService $assignmentService): void
    {
        if (!$this->currentOrder) {
            return;
        }

        if (!empty($this->driverNotes)) {
            $this->currentOrder->update([
                'special_instructions' => trim(($this->currentOrder->special_instructions ?? '') . " | Nota Driver: " . $this->driverNotes)
            ]);
        }

        $assignmentService->markAsPickedUp($this->currentOrder);

        $this->reset(['driverNotes', 'allItemsCorrect']);
        $this->loadActiveOrder();
    }

    /**
     * Paso 2: El repartidor confirma entrega al cliente.
     */
    public function completeDelivery(DriverAssignmentService $assignmentService): void
    {
        if (!$this->currentOrder) {
            return;
        }

        if ($this->paymentOutcome !== 'paid_correctly') {
            $this->currentOrder->update([
                'special_instructions' => trim(($this->currentOrder->special_instructions ?? '') . " | Incidencia Entrega: [{$this->paymentOutcome}] " . $this->incidentNotes)
            ]);
        }

        $assignmentService->completeOrderAndPullNext($this->currentOrder);

        $this->reset(['paymentOutcome', 'incidentNotes']);
        $this->loadActiveOrder();
    }

    public function render()
    {
        return view('livewire.driver-tasks-manager')
            ->layout('layouts.app');
    }
}