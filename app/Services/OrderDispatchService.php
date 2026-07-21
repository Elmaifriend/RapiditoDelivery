<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Driver;
use App\Enums\DriverStatus;
use App\Enums\DeliveryStatus;
use Illuminate\Support\Facades\DB;

class OrderDispatchService
{
    /**
     * Procesa la orden cuando el restaurante la marca como lista.
     */
    public function dispatchOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Se obtiene la ciudad a través del negocio (propiedad transitiva)
            $cityId = $order->business->city_id;

            // Busca repartidores disponibles en esa ciudad
            $availableDriver = Driver::where('city_id', $cityId)
                ->where('status', DriverStatus::AVAILABLE)
                ->inRandomOrder()
                ->first();

            if ($availableDriver) {
                // ESCENARIO 1: Hay repartidor disponible
                $this->assignOrderToDriver($order, $availableDriver);
            } else {
                // ESCENARIO 2: No hay repartidores disponibles -> Se encola
                $order->update([
                    'delivery_status' => DeliveryStatus::WAITING_DRIVER,
                    'driver_id' => null,
                ]);
            }
        });
    }

    /**
     * Vincula la orden con el repartidor y actualiza sus estados.
     */
    public function assignOrderToDriver(Order $order, Driver $driver): void
    {
        // 1. Asignar la orden y cambiar su estado
        $order->update([
            'driver_id' => $driver->id,
            'delivery_status' => DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT,
        ]);

        // 2. Cambiar el estado del repartidor a "Camino al restaurante"
        $driver->update([
            'status' => DriverStatus::HEADING_TO_RESTAURANT,
        ]);
    }
}