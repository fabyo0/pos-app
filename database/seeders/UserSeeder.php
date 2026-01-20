<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📝 Creating users...');

        // Super Admin
        $superAdmin = User::factory()->withoutTwoFactor()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('super_admin');
        $this->command->line('  ✓ Super Admin: superadmin@example.com');

        // Admin
        $admin = User::factory()->withoutTwoFactor()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');
        $this->command->line('  ✓ Admin: admin@example.com');

        // Manager
        $manager = User::factory()->withoutTwoFactor()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');
        $this->command->line('  ✓ Manager: manager@example.com');

        // Cashiers
        $cashierEmails = ['cashier1@example.com', 'cashier2@example.com', 'cashier3@example.com'];
        foreach ($cashierEmails as $index => $email) {
            $cashier = User::factory()->withoutTwoFactor()->create([
                'name' => 'Cashier ' . ($index + 1),
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
            $cashier->assignRole('cashier');
        }
        $this->command->line('  ✓ Cashiers: cashier1@example.com, cashier2@example.com, cashier3@example.com');

        // Warehouse
        $warehouse = User::factory()->withoutTwoFactor()->create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('password'),
        ]);
        $warehouse->assignRole('warehouse');
        $this->command->line('  ✓ Warehouse: warehouse@example.com');

        // Accountant
        $accountant = User::factory()->withoutTwoFactor()->create([
            'name' => 'Accountant',
            'email' => 'accountant@example.com',
            'password' => Hash::make('password'),
        ]);
        $accountant->assignRole('accountant');
        $this->command->line('  ✓ Accountant: accountant@example.com');

        // Viewer
        $viewer = User::factory()->withoutTwoFactor()->create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
        ]);
        $viewer->assignRole('viewer');
        $this->command->line('  ✓ Viewer: viewer@example.com');

        // ============================================
        // 🎯 DEMO USER - Public Demo Account
        // ============================================
        $demo = User::factory()->withoutTwoFactor()->create([
            'name' => 'Demo User',
            'email' => 'demo@demo.com',
            'password' => Hash::make('demo'),
            'email_verified_at' => now(),
        ]);
        $demo->assignRole('demo');
        $this->command->line('  ✓ Demo User: demo@demo.com (READ-ONLY)');

        // Random users with random roles
        $roles = ['cashier', 'warehouse', 'viewer'];
        User::factory(5)->withoutTwoFactor()->create()->each(function (User $user) use ($roles): void {
            $user->assignRole($roles[array_rand($roles)]);
        });
        $this->command->line('  ✓ 5 random users created');

        $this->command->newLine();
        $this->command->info('🎉 ' . User::count() . ' users created successfully!');
        $this->command->newLine();

        // Summary table
        $this->command->table(
            ['Email', 'Password', 'Role', 'Access Level'],
            [
                ['superadmin@example.com', 'password', 'Super Admin', 'Full Access'],
                ['admin@example.com', 'password', 'Admin', 'Almost Full'],
                ['manager@example.com', 'password', 'Manager', 'Business Ops'],
                ['cashier1@example.com', 'password', 'Cashier', 'POS Operations'],
                ['warehouse@example.com', 'password', 'Warehouse', 'Inventory'],
                ['accountant@example.com', 'password', 'Accountant', 'Financial'],
                ['viewer@example.com', 'password', 'Viewer', 'Read Only'],
                ['demo@demo.com', 'demo', 'Demo', '👁️ View Only (Public)'],
            ],
        );

        $this->command->newLine();
        $this->command->warn('📢 Public Demo Credentials:');
        $this->command->info('   Email: demo@demo.com');
        $this->command->info('   Password: demo');
        $this->command->newLine();
    }
}
