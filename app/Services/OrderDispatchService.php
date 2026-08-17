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
            // Aseguramos la carga de la relación del negocio/ciudad
            if (!$order->relationLoaded('business')) {
                $order->load('business');
            }

            $cityId = $order->business?->city_id;

            if (!$cityId) {
                return;
            }

            // Repartidor ONLINE, IDLE y sin pedidos activos en curso
            $availableDriver = Driver::where('city_id', $cityId)
                ->where('availability_status', DriverAvailability::ONLINE)
                ->where('operational_status', DriverOperationalStatus::IDLE)
                ->whereDoesntHave('orders', function ($query) {
                    $query->whereIn('delivery_status', [
                        DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT,
                        DeliveryStatus::PICKED_UP,
                        DeliveryStatus::ON_THE_WAY,
                    ]);
                })
                ->lockForUpdate()
                ->inRandomOrder()
                ->first();

            if ($availableDriver) {
                $this->assignOrderToDriver($order, $availableDriver);
                $driverToNotify = $availableDriver;
            } else {
                // Si están ocupados, encolar pedido
                $order->update([
                    'delivery_status' => DeliveryStatus::WAITING_DRIVER,
                    'driver_id'       => null,
                ]);
            }
        });

        // Notificar por WhatsApp fuera del bloqueo de la transacción DB
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
            'driver_id'       => $driver->id,
            'delivery_status' => DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT,
        ]);

        $driver->update([
            'operational_status' => DriverOperationalStatus::HEADING_TO_RESTAURANT,
        ]);
    }
}