<?php

namespace App\Services;

use App\Enums\DriverStatus;
use App\Models\Business;
use App\Models\Driver;

class DriverAvailabilityService
{
    /**
     * Verifica si hay repartidores trabajando (no inactivos) en la ciudad del negocio especificado.
     *
     * @param Business|int|null $business Instancia del negocio o su ID
     * @return bool
     */
    public function hasAvailableDriversForBusiness(Business|int|null $business): bool
    {
        if (! $business) {
            return false;
        }

        // Si se pasa un ID en lugar del modelo, resolvemos la ciudad
        $cityId = $business instanceof Business 
            ? $business->city_id 
            : Business::where('id', $business)->value('city_id');

        if (! $cityId) {
            return false;
        }

        return Driver::where('city_id', $cityId)
            ->where('status', '!=', DriverStatus::INACTIVE)
            ->exists();
    }

    /**
     * Verifica si hay repartidores trabajando (no inactivos) por ID de ciudad.
     *
     * @param int|null $cityId
     * @return bool
     */
    public function hasAvailableDriversInCity(?int $cityId): bool
    {
        if (! $cityId) {
            return false;
        }

        return Driver::where('city_id', $cityId)
            ->where('status', '!=', DriverStatus::INACTIVE)
            ->exists();
    }
}