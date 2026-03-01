<?php

namespace App\Services;

use App\Models\ServiceZone;

class DeliveryPriceResolver
{
    public function resolve(float $lat, float $lng): ?float
    {
        // 1️⃣ Buscar ServiceZone activa
        $serviceZone = ServiceZone::active()
            ->get()
            ->first(fn ($zone) => $zone->contains($lat, $lng));

        if (!$serviceZone) {
            return null;
        }

        // 2️⃣ Buscar DeliveryZone dentro de esa zona
        $zones = $serviceZone->deliveryZones()
            ->active()
            ->whereBboxContains($lat, $lng)
            ->orderBy('priority')
            ->get();

        foreach ($zones as $zone) {
            if ($zone->contains($lat, $lng)) {
                return (float) $zone->delivery_price;
            }
        }

        return null;
    }
}