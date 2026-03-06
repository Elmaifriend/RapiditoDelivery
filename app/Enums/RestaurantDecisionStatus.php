<?php

namespace App\Enums;

enum RestaurantDecisionStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case PARTIAL_PROPOSAL = 'partial_proposal';
}