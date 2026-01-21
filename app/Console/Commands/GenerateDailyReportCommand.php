<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RoleType;
use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\DailyReportReady;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

final class GenerateDailyReportCommand extends Command
{
    protected $signature = 'notifications:generate-daily-report';

    protected $description = 'Generate and send daily sales report to admins';

    public function handle(): int
    {
        $this->info('Generating daily report...');

        $yesterday = now()->subDay();

        $statistics = $this->gatherStatistics($yesterday);

        $admins = User::role([
            RoleType::SUPER_ADMIN->value,
            RoleType::ADMIN->value,
        ])->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found to notify.');
            return self::SUCCESS;
        }

        Notification::send($admins, new DailyReportReady($yesterday, $statistics));

        $this->info('Daily report sent to ' . $admins->count() . ' admin(s).');

        return self::SUCCESS;
    }

    private function gatherStatistics(\Carbon\Carbon $date): array
    {
        $sales = Sale::whereDate('created_at', $date)->get();

        $topItems = Sale::whereDate('sales.created_at', $date)
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->selectRaw('items.name, SUM(sale_items.quantity) as total_sold')
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->pluck('total_sold', 'name')
            ->toArray();

        $lowStockThreshold = config('notifications.low_stock_threshold', 10);

        $lowStockCount = Item::where('quantity', '<=', $lowStockThreshold)->count();

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'top_items' => $topItems,
            'low_stock_count' => $lowStockCount,
        ];
    }
}
