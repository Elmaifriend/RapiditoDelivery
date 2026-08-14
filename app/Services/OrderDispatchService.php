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
    public function __construct(
        protected WhatsAppNotifierService $notifier
    ) {}

    /**
     * Procesa la orden cuando el restaurante la marca como lista.
     */
    public function dispatchOrder(Order $order): void
    {
        $driverToNotify = null;

        DB::transaction(function () use ($order, &$driverToNotify) {
            // Aseguramos que la relación del negocio esté cargada
            if (!$order->relationLoaded('business')) {
                $order->load('business');
            }

            $cityId = $order->business->city_id;

            // Busca repartidores CONECTADOS y LIBRES con bloqueo para evitar condiciones de carrera
            $availableDriver = Driver::where('city_id', $cityId)
                ->where('availability_status', DriverAvailability::ONLINE)
                ->where('operational_status', DriverOperationalStatus::IDLE)
                ->lockForUpdate()
                ->inRandomOrder()
                ->first();

            if ($availableDriver) {
                $this->assignOrderToDriver($order, $availableDriver);
                $driverToNotify = $availableDriver;
            } else {
                $order->update([
                    'delivery_status' => DeliveryStatus::WAITING_DRIVER,
                    'driver_id' => null,
                ]);
            }
        });

        // Notificar por WhatsApp fuera de la transacción DB
        if ($driverToNotify) {
            $this->notifier->notifyDriverNewOrderAssignment($order, $driverToNotify);
        }
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