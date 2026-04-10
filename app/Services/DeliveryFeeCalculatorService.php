<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Models\DeliveryZoneFare;

class DeliveryFeeCalculatorService
{
    public function calculate(float $businessLat, float $businessLng, ?float $customerLat = null, ?float $customerLng = null): ?float
    {
        $originZone = $this->findZone($businessLat, $businessLng);

        // Si el cliente no tiene dirección aún, estimamos usando la tarifa base del restaurante
        if ($customerLat === null || $customerLng === null) {
            return $originZone ? (float) $originZone->delivery_price : null;
        }

        $destinationZone = $this->findZone($customerLat, $customerLng);

        // Si el cliente está fuera de todas las zonas de cobertura
        if (!$destinationZone) {
            return null;
        }

        // Si el restaurante no está en zona pero el cliente sí
        if (!$originZone) {
            return (float) $destinationZone->delivery_price;
        }

        // Buscar tarifa específica (Ej: De Ciudad a La Choya)
        return $this->getFarePrice($originZone->id, $destinationZone->id) 
            ?? (float) $destinationZone->delivery_price;
    }

    private function getFarePrice(int $originId, int $destinationId): ?float
    {
        $fare = DeliveryZoneFare::active()
            ->where('from_zone_id', $originId)
            ->where('to_zone_id', $destinationId)
            ->first();

        return $fare ? (float) $fare->price : null;
    }

    private function findZone(float $lat, float $lng): ?DeliveryZone
    {
        // Query manual usando el BBOX (Sin scopes)
        $potentialZones = DeliveryZone::active()
            ->where('bbox_min_lat', '<=', $lat)
            ->where('bbox_max_lat', '>=', $lat)
            ->where('bbox_min_lng', '<=', $lng)
            ->where('bbox_max_lng', '>=', $lng)
            ->orderBy('priority', 'desc')
            ->get();

        // Validamos matemáticamente y retornamos el primero que coincida
        return $potentialZones->first(fn($zone) => $zone->contains($lat, $lng));
    }
}