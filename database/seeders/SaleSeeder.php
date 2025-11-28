<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sale::factory(30)
            ->create()
            ->each(function (Sale $sale) {
                $items = Item::factory(rand(1, 5))->create();
                $total = 0;

                foreach ($items as $item) {
                    $quantity = rand(1, 3);
                    SalesItem::factory()->create([
                        'sale_id' => $sale->id,
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                        'price' => $item->price,
                    ]);
                    $total += $item->price * $quantity;
                }

                $sale->update([
                    'total' => $total,
                    'paid_amount' => $total - $sale->discount,
                ]);

            });
    }
}
