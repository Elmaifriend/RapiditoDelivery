<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DriverOperationalStatus: string implements HasLabel, HasColor, HasIcon
{
    case IDLE = 'idle';                         // Libre (esperando que le asignen pedido)
    case HEADING_TO_RESTAURANT = 'heading_to_restaurant'; // En camino al local
    case DELIVERING = 'delivering';             // En camino al cliente
    case BREAK = 'break';                       // En descanso temporal (opcional)

    public function getLabel(): ?string
    {
        return match ($this) {
            self::IDLE => 'Libre',
            self::HEADING_TO_RESTAURANT => 'Camino al Restaurante',
            self::DELIVERING => 'Entregando',
            self::BREAK => 'En Descanso',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::IDLE => 'success',
            self::HEADING_TO_RESTAURANT => 'warning',
            self::DELIVERING => 'info',
            self::BREAK => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::IDLE => 'heroicon-m-clock',
            self::HEADING_TO_RESTAURANT => 'heroicon-m-building-storefront',
            self::DELIVERING => 'heroicon-m-truck',
            self::BREAK => 'heroicon-m-pause-circle',
        };
    }
}