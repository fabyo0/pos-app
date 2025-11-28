<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\CustomerSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\SaleSeeder;
use Database\Seeders\UserSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,
            PaymentMethodSeeder::class,
            ItemSeeder::class,
            SaleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
