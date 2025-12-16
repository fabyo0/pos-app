<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesItem>
 */
final class SalesItemFactory extends Factory
{
    protected $model = SalesItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = Item::factory()->create();

        return [
            'sale_id' => Sale::factory(),
            'item_id' => $item->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $item->price,
        ];
    }
}
