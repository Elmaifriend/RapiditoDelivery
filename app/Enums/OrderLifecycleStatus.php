<?php

namespace App\Enums;

enum OrderLifecycleStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case ACCEPTED_BY_RESTAURANT = 'accepted_by_restaurant';
    case IN_PREPARATION = 'in_preparation';
    case READY = 'ready';
    case IN_DELIVERY = 'in_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}