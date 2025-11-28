<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Database\Seeder;

final class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::factory(20)->create()->each(function (Item $item): void {
            Inventory::factory()->create([
                'item_id' => $item->id,
            ]);
        });
    }
}
