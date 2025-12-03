<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CustomerSeeder::class,
            PaymentMethodSeeder::class,
            ItemSeeder::class,
            SaleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
