<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DriverAvailability: string implements HasLabel, HasColor, HasIcon
{
    case ONLINE = 'online';     // En turno / Conectado
    case OFFLINE = 'offline';   // Fuera de servicio / Desconectado

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ONLINE => 'Conectado',
            self::OFFLINE => 'Desconectado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ONLINE => 'success',
            self::OFFLINE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ONLINE => 'heroicon-m-check-circle',
            self::OFFLINE => 'heroicon-m-x-circle',
        };
    }
}