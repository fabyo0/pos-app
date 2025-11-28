<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
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
