<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    case PENDING = 1;
    case PROCESSING = 2;
    case SHIPPED = 3;
    case DELIVERED = 4;
    case CANCELLED = 5;
}