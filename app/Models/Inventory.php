<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InventoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $item_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item $item
 *
 * @method static InventoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Inventory newModelQuery()
 * @method static Builder<static>|Inventory newQuery()
 * @method static Builder<static>|Inventory query()
 * @method static Builder<static>|Inventory whereCreatedAt($value)
 * @method static Builder<static>|Inventory whereId($value)
 * @method static Builder<static>|Inventory whereItemId($value)
 * @method static Builder<static>|Inventory whereQuantity($value)
 * @method static Builder<static>|Inventory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Inventory extends Model
{
    /** @use HasFactory<InventoryFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(related: Item::class, foreignKey: 'item_id');
    }
}
