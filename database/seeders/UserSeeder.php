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

        User::factory(10)->withoutTwoFactor()->create()->each(function (User $user): void {
            $user->assignRole(RoleType::CASHIER->value);
        });
    }
}
