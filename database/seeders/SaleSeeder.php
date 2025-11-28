<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Database\Seeder;

final class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sale::factory(30)
            ->create()
            ->each(function (Sale $sale): void {
                $items = Item::factory(random_int(1, 5))->create();
                $total = 0;

                foreach ($items as $item) {
                    $quantity = random_int(1, 3);
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
