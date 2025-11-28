<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SalesItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $sale_id
 * @property int $item_id
 * @property int $quantity
 * @property numeric $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item $item
 * @property-read Sale $sale
 * @method static SalesItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|SalesItem newModelQuery()
 * @method static Builder<static>|SalesItem newQuery()
 * @method static Builder<static>|SalesItem query()
 * @method static Builder<static>|SalesItem whereCreatedAt($value)
 * @method static Builder<static>|SalesItem whereId($value)
 * @method static Builder<static>|SalesItem whereItemId($value)
 * @method static Builder<static>|SalesItem wherePrice($value)
 * @method static Builder<static>|SalesItem whereQuantity($value)
 * @method static Builder<static>|SalesItem whereSaleId($value)
 * @method static Builder<static>|SalesItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
final class SalesItem extends Model
{
    /** @use HasFactory<SalesItemFactory> */
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(related: Sale::class, foreignKey: 'sale_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(related: Item::class, foreignKey: 'item_id');
    }
}
