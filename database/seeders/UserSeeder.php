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
            ['Email', 'Password', 'Role'],
            [
                ['superadmin@example.com', 'password', 'Super Admin'],
                ['admin@example.com', 'password', 'Admin'],
                ['manager@example.com', 'password', 'Manager'],
                ['cashier1@example.com', 'password', 'Cashier'],
                ['warehouse@example.com', 'password', 'Warehouse'],
                ['accountant@example.com', 'password', 'Accountant'],
                ['viewer@example.com', 'password', 'Viewer'],
            ],
        );
    }
}
