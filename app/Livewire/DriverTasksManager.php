<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Driver;
use App\Enums\DeliveryStatus;
use App\Enums\DeliveryOutcome;
use App\Services\DriverAssignmentService;

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
        if (!$this->driver) {
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
        if (!$this->currentOrder) {
            return;
        }

        // Si el repartidor dejó notas al recoger el paquete
        if (!empty($this->driverNotes)) {
            $this->currentOrder->update([
                'delivery_notes' => trim(($this->currentOrder->delivery_notes ?? '') . " | Recogida: " . $this->driverNotes)
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

        // Convertir el string del formulario al Enum
        $outcome = DeliveryOutcome::tryFrom($this->paymentOutcome) ?? DeliveryOutcome::PAID_CORRECTLY;

        // Guardar las notas e incidentes en la orden si existen
        $this->currentOrder->update([
            'delivery_outcome' => $outcome,
            'delivery_notes'   => !empty($this->incidentNotes) 
                ? trim(($this->currentOrder->delivery_notes ?? '') . " | Entrega: " . $this->incidentNotes)
                : $this->currentOrder->delivery_notes,
        ]);

        // Delegar el cierre de la orden y la reasignación al servicio
        $assignmentService->completeOrderAndPullNext($this->currentOrder, $outcome);

        $this->reset(['paymentOutcome', 'incidentNotes']);
        $this->loadActiveOrder();
    }

    public function render()
    {
        return view('livewire.driver-tasks-manager');
    }
}