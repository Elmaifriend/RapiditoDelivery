<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case WAITING_DRIVER = 'waiting_driver';
    case DRIVER_HEADING_TO_RESTAURANT = 'driver_heading_to_restaurant';
    case PICKED_UP = 'picked_up';
    case ON_THE_WAY = 'on_the_way';
    case DELIVERED = 'delivered';
}