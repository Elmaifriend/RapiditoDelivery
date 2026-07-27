<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DriverStatus: string implements HasLabel, HasColor, HasIcon
{
    case AVAILABLE = 'available';
    case HEADING_TO_RESTAURANT = 'heading_to_restaurant';
    case DELIVERING = 'delivering';
    case INACTIVE = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponible',
            self::HEADING_TO_RESTAURANT => 'Camino al Restaurante',
            self::DELIVERING => 'Entregando',
            self::INACTIVE => 'Inactivo',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::HEADING_TO_RESTAURANT => 'warning',
            self::DELIVERING => 'info',
            self::INACTIVE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::AVAILABLE => 'heroicon-m-check-circle',
            self::HEADING_TO_RESTAURANT => 'heroicon-m-map-pin',
            self::DELIVERING => 'heroicon-m-truck',
            self::INACTIVE => 'heroicon-m-x-circle',
        };
    }
}
