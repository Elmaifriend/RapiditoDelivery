<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case REFUND_PENDING = 'refund_pending';
    case REFUNDED = 'refunded';
}