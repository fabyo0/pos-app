<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->withoutTwoFactor()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
        ]);

        $admin->assignRole(RoleType::ADMIN->value);

        $cashier = User::factory()->withoutTwoFactor()->create()->each(fn(User $user) => $user->assignRole(RoleType::CASHIER->value));

        $cashier->assignRole(RoleType::CASHIER->value);

    }
}
