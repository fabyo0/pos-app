<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            // Items
            'items.view',
            'items.create',
            'items.edit',
            'items.delete',

            // Inventory
            'inventory.view',
            'inventory.edit',

            // Sales
            'sales.view',
            'sales.create',
            'sales.delete',

            // Payment Methods
            'payment-methods.view',
            'payment-methods.create',
            'payment-methods.edit',
            'payment-methods.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $cashier = Role::create(['name' => 'cashier']);
        $cashier->givePermissionTo([
            'customers.view',
            'customers.create',
            'items.view',
            'inventory.view',
            'sales.view',
            'sales.create',
            'payment-methods.view',
        ]);
    }
}
