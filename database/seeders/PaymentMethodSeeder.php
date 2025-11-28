<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

final class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::factory()->createMany([
            ['name' => 'Cash'],
            ['name' => 'Card'],
            ['name' => 'Mobile Money'],
        ]);
    }
}
