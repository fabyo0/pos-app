<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleType: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case CASHIER = 'cashier';
    case WAREHOUSE = 'warehouse';
    case ACCOUNTANT = 'accountant';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
            self::CASHIER => 'Cashier',
            self::WAREHOUSE => 'Warehouse',
            self::ACCOUNTANT => 'Accountant',
            self::VIEWER => 'Viewer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'red',
            self::ADMIN => 'blue',
            self::MANAGER => 'green',
            self::CASHIER => 'yellow',
            self::WAREHOUSE => 'orange',
            self::ACCOUNTANT => 'indigo',
            self::VIEWER => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->toArray();
    }
}
