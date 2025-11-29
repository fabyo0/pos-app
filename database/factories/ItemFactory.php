<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
final class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'sku' => $this->faker->unique()->numerify('SKU-#####'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement([ItemStatus::ACTIVE->value, ItemStatus::INACTIVE->value]),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => ItemStatus::INACTIVE->value]);
    }
}
