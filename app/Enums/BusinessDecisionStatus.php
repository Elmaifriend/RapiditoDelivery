<?php

namespace App\Enums;

enum BusinessDecisionStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case PARTIAL_PROPOSAL = 'partial_proposal';
}