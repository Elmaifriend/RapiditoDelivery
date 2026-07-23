<?php

namespace App\Enums;

enum DeliveryOutcome: string
{
    case PAID_CORRECTLY = 'paid_correctly';
    case CLIENT_REFUSED_PAYMENT = 'client_refused_payment';
    case CLIENT_REFUSED_DELIVERY = 'client_refused_delivery';
    case CLIENT_NOT_FOUND = 'client_not_found';
    case INCIDENT_CANCELLED = 'incident_cancelled';

    /**
     * Devuelve una etiqueta amigable para mostrar en vistas o tablas del panel.
     */
    public function label(): string
    {
        return match ($this) {
            self::PAID_CORRECTLY => 'Cobrado correctamente',
            self::CLIENT_REFUSED_PAYMENT => 'Cliente rehusó pagar',
            self::CLIENT_REFUSED_DELIVERY => 'Cliente rehusó recibir el pedido',
            self::CLIENT_NOT_FOUND => 'Cliente no localizado',
            self::INCIDENT_CANCELLED => 'Cancelado por incidencia',
        };
    }

    /**
     * Devuelve un color de Bootstrap / Tailwind para etiquetas visuales o badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::PAID_CORRECTLY => 'success',
            self::CLIENT_REFUSED_PAYMENT, self::CLIENT_REFUSED_DELIVERY => 'danger',
            self::CLIENT_NOT_FOUND, self::INCIDENT_CANCELLED => 'warning',
        };
    }
}