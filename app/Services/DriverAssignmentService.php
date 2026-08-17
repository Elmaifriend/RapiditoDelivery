<?php

namespace App\Services;

use App\Enums\DeliveryOutcome;
use App\Enums\DeliveryStatus;
use App\Enums\DriverOperationalStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DriverAssignmentService
{
    public function __construct(
        protected OrderDispatchService $dispatchService,
        protected WhatsAppNotifierService $notifier
    ) {}

    /**
     * El repartidor indica que ya recogió la orden en el restaurante.
     */
    public function markAsPickedUp(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'delivery_status' => DeliveryStatus::ON_THE_WAY,
            ]);

            if ($order->driver) {
                $order->driver->update([
                    'operational_status' => DriverOperationalStatus::DELIVERING,
                ]);
            }
        });
    }

    /**
     * Finaliza la entrega actual sin asignar un nuevo pedido (Repartidor OFFLINE / Turno terminado).
     */
    public function completeDelivery(Order $order, DeliveryOutcome $outcome = DeliveryOutcome::PAID_CORRECTLY): void
    {
        DB::transaction(function () use ($order, $outcome) {
            $driver = $order->driver;

            $orderData = [
                'delivery_status'  => DeliveryStatus::DELIVERED,
                'lifecycle_status' => OrderLifecycleStatus::COMPLETED,
            ];

            if ($outcome === DeliveryOutcome::PAID_CORRECTLY) {
                $orderData['payment_status'] = PaymentStatus::PAID;
            }

            $order->update($orderData);

            if ($driver) {
                $driver->update([
                    'operational_status' => DriverOperationalStatus::IDLE,
                ]);
            }
        });
    }

    /**
     * El repartidor finaliza la entrega actual y jala el siguiente pedido pendiente en cola (FIFO).
     */
    public function completeOrderAndPullNext(Order $order, DeliveryOutcome $outcome = DeliveryOutcome::PAID_CORRECTLY): void
    {
        $nextOrder = null;
        $driverToNotify = null;

        DB::transaction(function () use ($order, $outcome, &$nextOrder, &$driverToNotify) {
            $driver = $order->driver;

            // 1. Finalizar la orden actual
            $orderData = [
                'delivery_status'  => DeliveryStatus::DELIVERED,
                'lifecycle_status' => OrderLifecycleStatus::COMPLETED,
            ];

            if ($outcome === DeliveryOutcome::PAID_CORRECTLY) {
                $orderData['payment_status'] = PaymentStatus::PAID;
            }

            $order->update($orderData);

            if (! $driver) {
                return;
            }

            // 2. Jalar la orden más antigua en cola (FIFO) para esta misma ciudad
            $nextOrder = Order::whereHas('business', function ($query) use ($driver) {
                $query->where('city_id', $driver->city_id);
            })
                ->where('delivery_status', DeliveryStatus::WAITING_DRIVER)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($nextOrder) {
                $this->dispatchService->assignOrderToDriver($nextOrder, $driver);
                $driverToNotify = $driver;
            } else {
                // Si no hay nada en cola, pasa a libre
                $driver->update([
                    'operational_status' => DriverOperationalStatus::IDLE,
                ]);
            }
        });

        // 3. Notificar al repartidor sobre el pedido jalado de la cola
        if ($nextOrder && $driverToNotify) {
            $this->notifier->notifyDriverNewOrderAssignment($nextOrder, $driverToNotify);
        }
    }
}