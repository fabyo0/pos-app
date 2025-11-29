<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
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
	final class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $item_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item $item
 * @method static InventoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Inventory newModelQuery()
 * @method static Builder<static>|Inventory newQuery()
 * @method static Builder<static>|Inventory query()
 * @method static Builder<static>|Inventory whereCreatedAt($value)
 * @method static Builder<static>|Inventory whereId($value)
 * @method static Builder<static>|Inventory whereItemId($value)
 * @method static Builder<static>|Inventory whereQuantity($value)
 * @method static Builder<static>|Inventory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	final class Inventory extends \Eloquent {}
}

namespace App\Models{
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
	final class Item extends \Eloquent {}
}

namespace App\Models{
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
	final class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
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
 * @property Carbon|null $deleted_at
 * @method static Builder<static>|Sale onlyTrashed()
 * @method static Builder<static>|Sale whereDeletedAt($value)
 * @method static Builder<static>|Sale withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Sale withoutTrashed()
 * @mixin \Eloquent
 */
	final class Sale extends \Eloquent {}
}

namespace App\Models{
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
 * @property string|null $deleted_at
 * @method static Builder<static>|SalesItem whereDeletedAt($value)
 * @mixin \Eloquent
 */
	final class SalesItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @property RoleType $role
 * @method static Builder<static>|User whereRole($value)
 * @property Carbon|null $deleted_at
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	final class User extends \Eloquent {}
}

