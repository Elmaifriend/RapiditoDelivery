<?php

namespace App\Enums;

enum OrderLifecycleStatus: string
{
    case PENDING = 'pending';     
    case CONFIRMED = 'confirmed'; 
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled'; 
}