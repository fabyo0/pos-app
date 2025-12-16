<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
final class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company_name' => $this->faker->optional(0.3)->company(),
            'tax_id' => $this->faker->optional(0.3)->numerify('##########'),
            'address' => $this->faker->optional()->streetAddress(),
            'city' => $this->faker->optional()->city(),
            'state' => $this->faker->optional()->state(),
            'postal_code' => $this->faker->optional()->postcode(),
            'country' => $this->faker->optional()->country(),
            'is_active' => $this->faker->boolean(),
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }

    public function active(): self
    {
        return $this->state(['is_active' => true]);
    }
}
