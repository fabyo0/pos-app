<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Customer::factory(10)->create();

        PaymentMethod::factory()->createMany([
            ['name' => 'Cash'],
            ['name' => 'Card'],
            ['name' => 'Mobile Money'],
        ]);

        Item::factory(20)->create()->each(function (Item $item): void {
            Inventory::factory()->create([
                'item_id' => $item->id,
            ]);
        });

        User::factory(10)->cashier()->withoutTwoFactor()->create();

        User::factory()->admin()->withoutTwoFactor()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
        ]);
    }
}
