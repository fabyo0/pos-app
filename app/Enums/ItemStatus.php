<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function torArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
