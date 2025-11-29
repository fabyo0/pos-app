<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Sale> $sales
 * @property-read int|null $sales_count
 * @method static CustomerFactory factory($count = null, $state = [])
 * @method static Builder<static>|Customer newModelQuery()
 * @method static Builder<static>|Customer newQuery()
 * @method static Builder<static>|Customer query()
 * @method static Builder<static>|Customer whereCreatedAt($value)
 * @method static Builder<static>|Customer whereEmail($value)
 * @method static Builder<static>|Customer whereId($value)
 * @method static Builder<static>|Customer whereName($value)
 * @method static Builder<static>|Customer wherePhone($value)
 * @method static Builder<static>|Customer whereUpdatedAt($value)
 * @property Carbon|null $deleted_at
 * @method static Builder<static>|Customer onlyTrashed()
 * @method static Builder<static>|Customer whereDeletedAt($value)
 * @method static Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Customer withoutTrashed()
 * @property string|null $company_name
 * @property string|null $tax_id
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property bool $is_active
 * @property string|null $notes
 * @property-read string|null $full_address
 * @method static Builder<static>|Customer whereAddress($value)
 * @method static Builder<static>|Customer whereCity($value)
 * @method static Builder<static>|Customer whereCompanyName($value)
 * @method static Builder<static>|Customer whereCountry($value)
 * @method static Builder<static>|Customer whereIsActive($value)
 * @method static Builder<static>|Customer whereNotes($value)
 * @method static Builder<static>|Customer wherePostalCode($value)
 * @method static Builder<static>|Customer whereState($value)
 * @method static Builder<static>|Customer whereTaxId($value)
 * @mixin \Eloquent
 */
final class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'tax_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'is_active',
        'notes',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(related: Sale::class, foreignKey: 'customer_id');
    }

    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return $parts === [] ? null : implode(', ', $parts);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
