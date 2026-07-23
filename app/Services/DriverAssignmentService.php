<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Order;
use App\Enums\DriverStatus;
use App\Enums\DeliveryStatus;
use App\Enums\DeliveryOutcome;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;

class DriverAssignmentService
{
    public function __construct(
        protected OrderDispatchService $dispatchService
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
                    'status' => DriverStatus::DELIVERING,
                ]);
            }
        });
    }

    /**
     * El repartidor finaliza la entrega actual y jala el siguiente pedido pendiente (FIFO).
     */
    public function completeOrderAndPullNext(Order $order, DeliveryOutcome $outcome = DeliveryOutcome::PAID_CORRECTLY): void
    {
        DB::transaction(function () use ($order, $outcome) {
            $driver = $order->driver;

            // 1. Determinar datos a actualizar en la orden actual
            $orderData = [
                'delivery_status'  => DeliveryStatus::DELIVERED,
                'lifecycle_status' => OrderLifecycleStatus::COMPLETED, // Usa 'lifecycle_status' como espera el modelo
            ];

            // Si el cliente pagó correctamente, marcamos el pago como PAID
            if ($outcome === DeliveryOutcome::PAID_CORRECTLY) {
                $orderData['payment_status'] = PaymentStatus::PAID;
            }

            $order->update($orderData);

            if (!$driver) {
                return;
            }

            // 2. Buscar la orden más vieja en espera (FIFO) en la misma ciudad
            $nextOrder = Order::whereHas('business', function ($query) use ($driver) {
                    $query->where('city_id', $driver->city_id);
                })
                ->where('delivery_status', DeliveryStatus::WAITING_DRIVER)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($nextOrder) {
                $this->dispatchService->assignOrderToDriver($nextOrder, $driver);
            } else {
                $driver->update([
                    'status' => DriverStatus::AVAILABLE,
                ]);
            }
        });
    }
}