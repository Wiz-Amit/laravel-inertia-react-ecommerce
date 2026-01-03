<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PLACED = 'placed';
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
