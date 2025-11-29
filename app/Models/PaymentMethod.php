<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Sale> $sales
 * @property-read int|null $sales_count
 * @method static PaymentMethodFactory factory($count = null, $state = [])
 * @method static Builder<static>|PaymentMethod newModelQuery()
 * @method static Builder<static>|PaymentMethod newQuery()
 * @method static Builder<static>|PaymentMethod query()
 * @method static Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static Builder<static>|PaymentMethod whereDescription($value)
 * @method static Builder<static>|PaymentMethod whereId($value)
 * @method static Builder<static>|PaymentMethod whereName($value)
 * @method static Builder<static>|PaymentMethod whereUpdatedAt($value)
 * @property bool $is_active
 * @method static Builder<static>|PaymentMethod whereIsActive($value)
 * @method static Builder<static>|PaymentMethod active()
 * @method static Builder<static>|PaymentMethod inactive()
 * @mixin \Eloquent
 */
final class PaymentMethod extends Model
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(related: Sale::class, foreignKey: 'payment_method_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('is_active', false);
    }
}
