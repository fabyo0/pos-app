<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemStatus;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property string $price
 * @property ItemStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Inventory|null $inventory
 * @property-read Collection<int, SalesItem> $salesItems
 * @property-read int|null $sales_items_count
 * @method static Builder<static>|Item active()
 * @method static ItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|Item inactive()
 * @method static Builder<static>|Item newModelQuery()
 * @method static Builder<static>|Item newQuery()
 * @method static Builder<static>|Item query()
 * @method static Builder<static>|Item whereCreatedAt($value)
 * @method static Builder<static>|Item whereId($value)
 * @method static Builder<static>|Item whereName($value)
 * @method static Builder<static>|Item wherePrice($value)
 * @method static Builder<static>|Item whereSku($value)
 * @method static Builder<static>|Item whereStatus($value)
 * @method static Builder<static>|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
final class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'status',
        'low_stock_threshold'
    ];

    public function inventory(): HasOne
    {
        return $this->hasOne(related: Inventory::class, foreignKey: 'item_id');
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(related: SalesItem::class, foreignKey: 'item_id');
    }

    protected function casts(): array
    {
        return [
            'status' => ItemStatus::class,
            'low_stock_threshold' => 'integer'
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', ItemStatus::ACTIVE->value);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', ItemStatus::INACTIVE->value);
    }
}
