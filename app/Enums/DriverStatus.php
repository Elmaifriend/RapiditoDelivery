<?php

namespace App\Enums;

enum DriverStatus: string
{
    case AVAILABLE = 'available';
    case HEADING_TO_RESTAURANT = 'heading_to_restaurant';
    case DELIVERING = 'delivering';
    case INACTIVE = 'inactive';
}