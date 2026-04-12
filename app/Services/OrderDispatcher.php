<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Driver;

class OrderDispatcher
{
    public function getAvailableDriversForOrder(Order $order)
    {
        // 1. Cargamos el negocio para no hacer N+1 queries
        $order->loadMissing('business');
        
        $cityId = $order->business->city_id;

        // 2. Filtramos los drivers de esa ciudad que estén disponibles
        return Driver::where('city_id', $cityId)
            ->where('status', 'available')
            // Aquí puedes agregar en el futuro: ->whereHas('serviceZones', ...)
            ->get();
    }
}