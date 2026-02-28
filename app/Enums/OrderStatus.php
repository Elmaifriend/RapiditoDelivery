<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMING_ORDER = 'confirming_order';
    case CONFIRMED = 'confirmed';
    case CONFIRMING_LOCATION = 'confirming_location';
    case LOCATION_CONFIRMED = 'location_confirmed';
    case RESTAURANT_PENDING = 'restaurant_pending';
    case RESTAURANT_ACCEPTED = 'restaurant_accepted';
    case PREPARING = 'preparing';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case ON_THE_WAY = 'on_the_way';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RESTAURANT_REJECTED = 'restaurant_rejected';
    case PARTIAL_UNAVAILABLE = 'partial_unavailable';
    case REFUND_PENDING = 'refund_pending';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function keys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function associative(): array
    {
        return array_combine(
            self::values(),
            self::values()
        );
    }
}