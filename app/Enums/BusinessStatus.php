<?php

namespace App\Enums;

enum BusinessStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING_APPROVAL = 'pending_approval';
}