<?php

namespace App\Services;

use App\Enums\DeliveryStatus;
use App\Enums\DriverAvailability;
use App\Enums\DriverOperationalStatus;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderDispatchService
{
    /**
     * Procesa la orden cuando el restaurante la marca como lista.
     */
    public function dispatchOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $cityId = $order->business->city_id;

            // Busca repartidores que estén CONECTADOS y LIBRES en esa ciudad
            $availableDriver = Driver::where('city_id', $cityId)
                ->where('availability', DriverAvailability::ONLINE)
                ->where('operational_status', DriverOperationalStatus::IDLE)
                ->inRandomOrder()
                ->first();

            if ($availableDriver) {
                $this->assignOrderToDriver($order, $availableDriver);
            } else {
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
        $order->update([
            'driver_id' => $driver->id,
            'delivery_status' => DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT,
        ]);

        $driver->update([
            'operational_status' => DriverOperationalStatus::HEADING_TO_RESTAURANT,
        ]);
    }
}