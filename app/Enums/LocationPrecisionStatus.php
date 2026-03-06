<?php

namespace App\Enums;

enum LocationPrecisionStatus: string
{
    case NOT_REQUESTED = 'not_requested';
    case REQUESTED = 'requested';
    case CONFIRMED = 'confirmed';
}