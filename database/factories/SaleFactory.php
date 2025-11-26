<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
final class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 50, 5000);
        $discount = $this->faker->randomFloat(2, 0, $total * 0.2);

        return [
            'customer_id' => Customer::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'total' => $total,
            'paid_amount' => $total - $discount,
            'discount' => $discount,
        ];
    }

    public function withoutCustomer(): static
    {
        return $this->state(['customer_id' => null]);
    }
}
