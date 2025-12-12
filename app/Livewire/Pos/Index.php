<?php

declare(strict_types=1);

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\PaymentMethod;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Index extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $items = [];

    /** @var Collection<int, Customer> */
    public $customers;

    /** @var Collection<int, PaymentMethod> */
    public $paymentMethods;

    public ?string $search = null;

    public ?string $customerSearch = null;

    public array $cart = [];

    public ?array $data = [];

    public $customerId = null;

    public $paymentMethodId = null;

    public int $paidAmount = 0;

    public int|float $discountAmount = 0;

    public function mount(): void
    {
        $this->items = Item::with('inventory')
            ->withWhereHas(relation: 'inventory', callback: function ($query): void {
                $query->where('quantity', '>', 0);
            })
            ->active()
            ->get()
            ->map(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => $item->price,
                'stock' => $item->inventory->quantity,
            ])
            ->toArray();

        $this->customers = Customer::all();
        $this->paymentMethods = PaymentMethod::select(['id', 'name'])->get();
    }

    #[Computed]
    public function filteredItems(): array
    {
        if (empty($this->search)) {
            return $this->items;
        }

        $search = mb_strtolower($this->search);

        return array_filter(
            $this->items,
            fn (array $item): bool => str_contains(mb_strtolower($item['name']), $search)
                || str_contains(mb_strtolower($item['sku']), $search),
        );
    }

    #[Computed]
    public function subtotal(): float|int
    {
        return collect($this->cart)
            ->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    #[Computed]
    public function tax(): float
    {
        return $this->subtotal * 0.15;
    }

    #[Computed]
    public function totalBeforeDiscount(): int|float
    {
        return $this->subtotal + $this->tax;
    }

    #[Computed]
    public function total(): int|float
    {
        return $this->totalBeforeDiscount - $this->discountAmount;
    }

    #[Computed]
    public function change(): int|float
    {
        return max(0, $this->paidAmount - $this->total);
    }

    public function addToCart(int $itemId): void
    {
        $item = collect($this->items)->firstWhere('id', $itemId);

        if ( ! $item) {
            Notification::make()->title('Item not found!')->danger()->send();

            return;
        }

        $stock = $item['stock'];

        if ($stock <= 0) {
            Notification::make()->title('Out of stock!')->danger()->send();

            return;
        }

        $currentQty = $this->cart[$itemId]['quantity'] ?? 0;

        if ($currentQty >= $stock) {
            Notification::make()->title("Only {$stock} in stock")->warning()->send();

            return;
        }

        $this->cart[$itemId] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'sku' => $item['sku'],
            'price' => $item['price'],
            'quantity' => $currentQty + 1,
        ];
    }

    public function removeCart(int $itemId): void
    {
        unset($this->cart[$itemId]);
    }

    public function updateQuantity($itemId, $quantity): void
    {
        $quantity = max(1, (int) $quantity);

        $inventory = Inventory::firstWhere('item_id', $itemId);

        if ($quantity > $inventory->quantity) {

            Notification::make()
                ->title("Cannot add more. Only {$inventory->quantity} in stock.")
                ->danger()
                ->send();
            $this->cart[$itemId]['quantity'] = $inventory->quantity;
        } else {
            $this->cart[$itemId]['quantity'] = $quantity;
        }
    }

    public function incrementQuantity(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            $item = collect($this->items)->firstWhere('id', $itemId);
            if ($this->cart[$itemId]['quantity'] < $item['stock']) {
                $this->cart[$itemId]['quantity']++;
            }
        }
    }

    public function decrementQuantity(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            if ($this->cart[$itemId]['quantity'] > 1) {
                $this->cart[$itemId]['quantity']--;
            } else {
                $this->removeCart($itemId);
            }
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function submit(): void
    {
        $this->form->getState();

    }

    #[Computed]
    public function filteredCustomers()
    {
        if (empty($this->customerSearch)) {
            return $this->customers;
        }

        $search = mb_strtolower($this->customerSearch);

        return $this->customers->filter(
            fn ($customer): bool => str_contains(mb_strtolower($customer->name), $search) ||
            str_contains(mb_strtolower($customer->phone ?? ''), $search) ||
            str_contains(mb_strtolower($customer->email ?? ''), $search),
        );
    }

    public function clearDiscount(): void
    {
        $this->discountAmount = 0;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

            ])
            ->statePath('data');
    }

    public function checkout(): void
    {
        dd($this->all());
    }

    public function render(): View
    {
        return view('livewire.pos.index');
    }
}
