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

        // Generate permissions from config
        $resources = config('permission-resources');

        if (empty($resources)) {
            $this->command->error('❌ permission-resources config not found!');

            return;
        }

        $this->command->info('📝 Creating permissions...');

        foreach ($resources as $resource => $config) {
            foreach ($config['permissions'] as $action) {
                Permission::firstOrCreate([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $this->command->info('✅ ' . Permission::count() . ' permissions created.');

        // Create Roles
        $this->command->info('📝 Creating roles...');

        // 1. Super Admin - Full access
        $superAdmin = $this->createRole(
            name: 'super_admin',
            description: 'Full system access with all permissions. Can manage everything including system settings.',
            color: 'red',
            sortOrder: 1,
            isSystem: true,
        );
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin - Almost full access
        $admin = $this->createRole(
            name: 'admin',
            description: 'Administrative access to most features except critical system operations.',
            color: 'blue',
            sortOrder: 2,
            isSystem: true,
        );
        $admin->syncPermissions(
            Permission::whereNotIn('name', [
                'roles.delete',
                'users.delete',
                'backups.delete',
                'settings.edit',
            ])->pluck('name')->toArray(),
        );

        // 3. Manager - Business operations
        $manager = $this->createRole(
            name: 'manager',
            description: 'Management level access for daily business operations and reporting.',
            color: 'green',
            sortOrder: 3,
            isSystem: false,
        );
        $manager->syncPermissions([
            // Dashboard
            'dashboard.view',
            // Customers
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.export',
            // Items
            'items.view', 'items.create', 'items.edit', 'items.delete', 'items.export',
            // Sales
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.export', 'sales.refund',
            // Inventory
            'inventory.view', 'inventory.manage', 'inventory.adjust', 'inventory.transfer',
            // Payment Methods
            'payment-methods.view', 'payment-methods.create', 'payment-methods.edit',
            // Backups (view only)
            'backups.view',
        ]);

        // 4. Cashier - POS operations
        $cashier = $this->createRole(
            name: 'cashier',
            description: 'Point of sale operations including sales creation and customer management.',
            color: 'yellow',
            sortOrder: 4,
            isSystem: false,
        );
        $cashier->syncPermissions([
            // Dashboard
            'dashboard.view',
            // Customers (limited)
            'customers.view', 'customers.create', 'customers.edit',
            // Items (view only)
            'items.view',
            // Sales
            'sales.view', 'sales.create',
            // Inventory (view only)
            'inventory.view',
            // Payment Methods (view only)
            'payment-methods.view',
        ]);

        // 5. Warehouse Staff - Inventory focused
        $warehouse = $this->createRole(
            name: 'warehouse',
            description: 'Warehouse and inventory management operations.',
            color: 'orange',
            sortOrder: 5,
            isSystem: false,
        );
        $warehouse->syncPermissions([
            // Dashboard
            'dashboard.view',
            // Items
            'items.view', 'items.create', 'items.edit',
            // Inventory (full)
            'inventory.view', 'inventory.manage', 'inventory.adjust', 'inventory.transfer',
        ]);

        // 6. Accountant - Financial access
        $accountant = $this->createRole(
            name: 'accountant',
            description: 'Financial reporting and sales data access for accounting purposes.',
            color: 'indigo',
            sortOrder: 6,
            isSystem: false,
        );
        $accountant->syncPermissions([
            // Dashboard
            'dashboard.view',
            // Customers (view & export)
            'customers.view', 'customers.export',
            // Items (view only)
            'items.view',
            // Sales (view & export)
            'sales.view', 'sales.export',
            // Payment Methods (view only)
            'payment-methods.view',
        ]);

        // 7. Viewer - Read only
        $viewer = $this->createRole(
            name: 'viewer',
            description: 'Read-only access to view information across the system.',
            color: 'gray',
            sortOrder: 7,
            isSystem: false,
        );
        $viewer->syncPermissions([
            'dashboard.view',
            'customers.view',
            'items.view',
            'sales.view',
            'inventory.view',
            'payment-methods.view',
        ]);

        $this->command->info('✅ ' . Role::count() . ' roles created.');
        $this->command->newLine();
        $this->command->info('🎉 Roles and permissions seeded successfully!');
    }

    private function createRole(
        string $name,
        string $description,
        string $color,
        int $sortOrder,
        bool $isSystem,
    ): Role {
        return Role::updateOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            [
                'description' => $description,
                'color' => $color,
                'sort_order' => $sortOrder,
                'is_system' => $isSystem,
            ],
        );
    }
}
