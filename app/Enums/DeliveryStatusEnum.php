<?php

namespace App\Enums;

enum DeliveryStatusEnum: int
{
    case PENDING_COURIER = 1;
    case COURIER_ACCEPTED = 2;
    case EN_ROUTE = 3;
    case DELIVERY_COMPLETED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING_COURIER => 'Pending Courier',
            self::COURIER_ACCEPTED => 'Courier Accepted Delivery',
            self::EN_ROUTE => 'En Route',
            self::DELIVERY_COMPLETED => 'Delivery Completed',
        };
    }

    public static function fromId(int $id): self
    {
        return match ($id) {
            self::PENDING_COURIER->value => self::PENDING_COURIER,
            self::COURIER_ACCEPTED->value => self::COURIER_ACCEPTED,
            self::EN_ROUTE->value => self::EN_ROUTE,
            self::DELIVERY_COMPLETED->value => self::DELIVERY_COMPLETED,
            default => throw new \InvalidArgumentException("Invalid status ID: $id"),
        };
    }
}