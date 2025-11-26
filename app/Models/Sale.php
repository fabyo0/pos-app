<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $payment_method_id
 * @property numeric $total
 * @property numeric $paid_amount
 * @property numeric $discount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer|null $customer
 * @property-read PaymentMethod|null $paymentMethod
 * @property-read Collection<int, SalesItem> $salesItems
 * @property-read int|null $sales_items_count
 * @method static SaleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Sale newModelQuery()
 * @method static Builder<static>|Sale newQuery()
 * @method static Builder<static>|Sale query()
 * @method static Builder<static>|Sale whereCreatedAt($value)
 * @method static Builder<static>|Sale whereCustomerId($value)
 * @method static Builder<static>|Sale whereDiscount($value)
 * @method static Builder<static>|Sale whereId($value)
 * @method static Builder<static>|Sale wherePaidAmount($value)
 * @method static Builder<static>|Sale wherePaymentMethodId($value)
 * @method static Builder<static>|Sale whereTotal($value)
 * @method static Builder<static>|Sale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
final class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'payment_method_id',
        'total',
        'paid_amount',
        'discount',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(related: Customer::class, foreignKey: 'customer_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(related: PaymentMethod::class, foreignKey: 'payment_method_id');
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(related: SalesItem::class, foreignKey: 'sale_id');
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal',
            'paid_amount' => 'decimal',
            'discount' => 'decimal',
        ];
    }
}
