<?php

namespace App\Livewire\Pages;

use App\Enums\DeliveryOutcome;
use App\Enums\DeliveryStatus;
use App\Enums\DriverAvailability;
use App\Models\Driver;
use App\Models\Order;
use App\Services\DriverAssignmentService;
use Livewire\Component;

class DriverTasksManager extends Component
{
    public Driver $driver;

    public ?Order $currentOrder = null;

    // Campos de reporte / supervisión
    public bool $allItemsCorrect = true;

    public string $driverNotes = '';

    // Campos de confirmación de entrega
    public string $paymentOutcome = DeliveryOutcome::PAID_CORRECTLY->value;

    public string $incidentNotes = '';

    public function mount(Driver $driver): void
    {
        $this->driver = $driver;
        $this->loadActiveOrder();
    }

    public function loadActiveOrder(): void
    {
        if (! $this->driver) {
            $this->currentOrder = null;

            return;
        }

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
        if (! $this->currentOrder) {
            return;
        }

        if (! empty($this->driverNotes)) {
            $this->currentOrder->update([
                'delivery_notes' => trim(($this->currentOrder->delivery_notes ?? '').' | Recogida: '.$this->driverNotes),
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
        if (! $this->currentOrder) {
            return;
        }

        $outcome = DeliveryOutcome::tryFrom($this->paymentOutcome) ?? DeliveryOutcome::PAID_CORRECTLY;

        $this->currentOrder->update([
            'delivery_outcome' => $outcome,
            'delivery_notes' => ! empty($this->incidentNotes)
                ? trim(($this->currentOrder->delivery_notes ?? '').' | Entrega: '.$this->incidentNotes)
                : $this->currentOrder->delivery_notes,
        ]);

        $this->driver->refresh();

        // Evalúa la disponibilidad laboral para asignar nuevo pedido o solo cerrar la orden actual
        if ($this->driver->availability === DriverAvailability::OFFLINE) {
            $assignmentService->completeDelivery($this->currentOrder, $outcome);
        } else {
            $assignmentService->completeOrderAndPullNext($this->currentOrder, $outcome);
        }

        $this->reset(['paymentOutcome', 'incidentNotes']);
        $this->loadActiveOrder();
    }

    public function render()
    {
        return view('livewire.pages.driver-tasks-manager', [
            'driverId' => $this->driver->id,
            'driver'   => $this->driver,
        ]);
    }
}