<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case CASHIER = 'cashier';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::CASHIER => 'Cashier',
        };
    }

    public function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
