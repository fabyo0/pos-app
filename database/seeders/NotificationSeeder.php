<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\DailyReportReady;
use App\Notifications\LowStockAlert;
use App\Notifications\SaleCompleted;
use App\Notifications\SaleFailed;
use App\Notifications\SystemError;
use App\Notifications\SystemWarning;
use Illuminate\Database\Seeder;
use Throwable;

final class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📝 Creating test notifications...');

        $admins = User::role([RoleType::SUPER_ADMIN->value, RoleType::ADMIN->value])->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Skipping notifications.');
            return;
        }

        foreach ($admins as $admin) {
            // Low Stock Alert
            $item = Item::first();
            if ($item) {
                $admin->notify(new LowStockAlert($item, 5));
                $admin->notify(new LowStockAlert($item, 0)); // Critical
                $this->command->line("  ✓ Low Stock Alerts sent to {$admin->email}");
            }

            // Sale Completed - try/catch ile
            try {
                $sale = Sale::first();
                if ($sale) {
                    $admin->notify(new SaleCompleted($sale));
                    $this->command->line("  ✓ Sale Completed sent to {$admin->email}");
                }
            } catch (Throwable $e) {
                $this->command->warn("  ⚠ Sale Completed skipped: {$e->getMessage()}");
            }

            // Sale Failed
            $admin->notify(new SaleFailed(null, 'Payment gateway timeout'));
            $this->command->line("  ✓ Sale Failed sent to {$admin->email}");

            // Daily Report
            $admin->notify(new DailyReportReady(now()->subDay(), [
                'total_sales' => 45,
                'total_revenue' => 12500.00,
                'top_items' => ['Product A' => 15, 'Product B' => 12, 'Product C' => 8],
                'low_stock_count' => 3,
            ]));
            $this->command->line("  ✓ Daily Report sent to {$admin->email}");

            // System Warning
            $admin->notify(new SystemWarning('High memory usage detected', [
                'memory_usage' => '85%',
                'threshold' => '80%',
            ]));
            $this->command->line("  ✓ System Warning sent to {$admin->email}");

            // System Error
            $admin->notify(new SystemError('Database connection failed', [
                'error_code' => 'DB_CONN_001',
                'attempted_at' => now()->toIso8601String(),
            ]));
            $this->command->line("  ✓ System Error sent to {$admin->email}");
        }

        $this->command->newLine();
        $this->command->info('🎉 Test notifications created successfully!');
    }
}
