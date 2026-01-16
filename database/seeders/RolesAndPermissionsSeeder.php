<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
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

        foreach ($resources as $resource => $config) {
            foreach ($config['permissions'] as $action) {
                Permission::firstOrCreate([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create Super Admin Role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'description' => 'Full system access with all permissions',
                'color' => 'red',
                'sort_order' => 1,
                'is_system' => true,
            ],
        );
        $superAdmin->givePermissionTo(Permission::all());

        // Create Admin Role
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description' => 'Administrative access to most features',
                'color' => 'blue',
                'sort_order' => 2,
                'is_system' => true,
            ],
        );
        $admin->givePermissionTo(Permission::whereNotIn('name', [
            'users.delete',
            'roles.delete',
        ])->get());

        // Create Manager Role
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'description' => 'Management level access without user management',
                'color' => 'green',
                'sort_order' => 3,
                'is_system' => false,
            ],
        );
        $manager->givePermissionTo([
            'dashboard.view',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'items.view', 'items.create', 'items.edit', 'items.delete',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.export',
            'inventory.view', 'inventory.manage',
            'payment-methods.view', 'payment-methods.create', 'payment-methods.edit', 'payment-methods.delete',
            'backups.view', 'backups.create',
        ]);

        // Create Cashier Role
        $cashier = Role::firstOrCreate(
            ['name' => 'cashier'],
            [
                'description' => 'Point of sale and basic operations',
                'color' => 'yellow',
                'sort_order' => 4,
                'is_system' => false,
            ],
        );
        $cashier->givePermissionTo([
            'dashboard.view',
            'customers.view', 'customers.create',
            'items.view',
            'sales.view', 'sales.create',
        ]);

        // Create Viewer Role
        $viewer = Role::firstOrCreate(
            ['name' => 'viewer'],
            [
                'description' => 'Read-only access to view information',
                'color' => 'gray',
                'sort_order' => 5,
                'is_system' => false,
            ],
        );
        $viewer->givePermissionTo([
            'dashboard.view',
            'customers.view',
            'items.view',
            'sales.view',
            'inventory.view',
        ]);

        // Assign super_admin role to first user
        $user = User::first();
        if ($user && ! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        $this->command->info('✅ Roles and permissions created successfully!');
    }
}
