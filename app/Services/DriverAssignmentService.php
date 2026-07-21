<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Order;
use App\Enums\DriverStatus;
use App\Enums\DeliveryStatus;
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

            // El repartidor pasa a estar en trayecto al domicilio
            $order->driver->update([
                'status' => DriverStatus::DELIVERING,
            ]);
        });
    }

    /**
     * El repartidor finaliza la entrega actual y jala el siguiente pedido pendiente (FIFO).
     */
    public function completeOrderAndPullNext(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $driver = $order->driver;

            // 1. Finalizar la orden actual
            $order->update([
                'delivery_status' => DeliveryStatus::DELIVERED,
            ]);

            // 2. Buscar la orden más vieja en espera (FIFO) en la misma ciudad
            $nextOrder = Order::whereHas('business', function ($query) use ($driver) {
                    $query->where('city_id', $driver->city_id);
                })
                ->where('delivery_status', DeliveryStatus::WAITING_DRIVER)
                ->orderBy('created_at', 'asc') // El pedido más antiguo primero
                ->lockForUpdate()
                ->first();

            if ($nextOrder) {
                // Si hay un pedido encolado, se le asigna de inmediato
                $this->dispatchService->assignOrderToDriver($nextOrder, $driver);
            } else {
                // Si no hay pedidos en espera, el repartidor queda libre
                $driver->update([
                    'status' => DriverStatus::AVAILABLE,
                ]);
            }
        });
    }
}