<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case UNKNOWN = "unknown";
    case CASH = 'cash';
    case TERMINAL = 'terminal';

    public function label(): string
    {
        return match($this) {
            self::UNKNOWN => "Desconocido",
            self::CASH => 'Efectivo',
            self::TERMINAL => 'Terminal',
        };
    }
}