<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Index extends Component
{
    #[Computed]
    public function todaySales(): array
    {
        $sales = Sale::whereDate('created_at', today());

        return [
            'total' => $sales->sum('total'),
            'count' => $sales->count(),
        ];
    }

    #[Computed]
    public function todayCustomers(): int
    {
        return Sale::whereDate('created_at', today())
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');
    }

    #[Computed]
    public function lowStockItems(): int
    {
        return Inventory::where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();
    }

    public function outOfStockItems(): int
    {
        return Inventory::where('quantity', '<=', 0)->count();
    }

    public function pendingPayments(): array
    {
        $sales = Sale::whereColumn('paid_amount', '<', 'total');

        return [
            'total' => $sales->sum(DB::raw('total - paid_amount')),
            'count' => $sales->count(),
        ];
    }

    #[Computed]
    public function totalCustomers(): int
    {
        return Customer::count();
    }

    #[Computed]
    public function totalItems(): int
    {
        return Item::active()->count();
    }

    #[Computed]
    public function monthSales(): array
    {
        $sales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        return [
            'total' => $sales->sum('total'),
            'count' => $sales->count(),
        ];
    }

    #[Computed]
    public function recentSales(): Collection
    {
        return Sale::with(['customer:id,name', 'paymentMethod:id,name'])
            ->withCount('salesItems')
            ->latest()
            ->take(5)
            ->get();
    }

    public function topSellingItems(): SupportCollection
    {
        return DB::table('sales_items')
            ->join('items', 'sales_items.item_id', 'items.id')
            ->select('items.name', DB::raw('SUM(sales_items.quantity) as total_sold'))
            ->whereMonth('sales_items.created_at', now()->month)
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function lowStockList(): Collection
    {
        return Inventory::with('item:id,name')
            ->where('quantity', '<=', 10)
            ->orderBy('quantity')
            ->get();
    }

    #[Computed]
    public function weeklySalesChart(): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo): array {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'date' => $date->toDateString(),
                'total' => Sale::whereDate('created_at', $date)->sum('total'),
            ];
        });

        return [
            'labels' => $days->pluck('label')->toArray(),
            'data' => $days->pluck('total')->toArray(),
        ];
    }

    public function paymentMethodsChart(): array
    {
        return Sale::whereMonth('created_at', now()->month)
            ->selectRaw('payment_method_id, SUM(total) as total')
            ->groupBy('payment_method_id')
            ->with('paymentMethod:id,name')
            ->get()
            ->map(callback: fn($sale): array => [
                'label' => $sale->paymentMethod?->name ?? 'Unknown',
                'total' => $sale->total,
            ])
            ->toArray();
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard.index');
    }
}
