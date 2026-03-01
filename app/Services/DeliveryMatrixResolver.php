<?php

namespace App\Services;

use App\Models\ServiceZone;
use App\Models\DeliveryZone;
use App\Models\DeliveryZoneFare;
use Location\Coordinate;

class DeliveryMatrixResolver
{
    public function resolve(
        float $restaurantLat,
        float $restaurantLng,
        float $customerLat,
        float $customerLng
    ): ?float {

        $fromZone = $this->findZoneByPoint($restaurantLat, $restaurantLng);
        $toZone   = $this->findZoneByPoint($customerLat, $customerLng);

        if (!$fromZone || !$toZone) {
            return null;
        }

        $fare = DeliveryZoneFare::active()
            ->where('from_zone_id', $fromZone->id)
            ->where('to_zone_id', $toZone->id)
            ->first();

        return $fare ? (float) $fare->price : null;
    }

    protected function findZoneByPoint(float $lat, float $lng): ?DeliveryZone
    {
        $serviceZone = ServiceZone::active()
            ->get()
            ->first(fn ($zone) => $zone->contains($lat, $lng));

        if (!$serviceZone) {
            return null;
        }

        $zones = $serviceZone->deliveryZones()
            ->active()
            ->whereBboxContains($lat, $lng)
            ->orderBy('priority')
            ->get();

        foreach ($zones as $zone) {
            if ($zone->contains($lat, $lng)) {
                return $zone;
            }
        }

        return null;
    }
}