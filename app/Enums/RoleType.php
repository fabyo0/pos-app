<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoleType: string implements HasLabel
{
    case ADMIN = 'admin';
    case CASHIER = 'cashier';

    public function getLabel(): string
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
