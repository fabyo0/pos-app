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
            self::SUPER_ADMIN => 'danger',
            self::ADMIN => 'info',
            self::MANAGER => 'success',
            self::CASHIER => 'warning',
            self::WAREHOUSE => 'orange',
            self::ACCOUNTANT => 'indigo',
            self::VIEWER => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'heroicon-o-shield-exclamation',
            self::ADMIN => 'heroicon-o-shield-check',
            self::MANAGER => 'heroicon-o-briefcase',
            self::CASHIER => 'heroicon-o-banknotes',
            self::WAREHOUSE => 'heroicon-o-cube',
            self::ACCOUNTANT => 'heroicon-o-calculator',
            self::VIEWER => 'heroicon-o-eye',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->toArray();
    }

    public static function fromName(string $name): ?self
    {
        return self::tryFrom($name);
    }

    public static function getColor(string $name): string
    {
        return self::tryFrom($name)?->color() ?? 'gray';
    }

    public static function getIcon(string $name): string
    {
        return self::tryFrom($name)?->icon() ?? 'heroicon-o-user';
    }

    public static function getLabel(string $name): string
    {
        return self::tryFrom($name)?->label() ?? ucfirst(str_replace('_', ' ', $name));
    }
}
