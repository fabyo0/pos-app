<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\RoleType;
use App\Models\Item;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

final class InventoryObserver
{
    public function updated(Item $item): void
    {
        if ( ! $item->wasChanged('quantity')) {
            return;
        }

        $threshold = $item->low_stock_threshold
            ?? config('notifications.low_stock_threshold', 10);

        if ($item->quantity > $threshold) {
            return;
        }

        if ($this->isDuplicateAlert($item)) {
            return;
        }

        $this->notifyAdmins($item);
        $this->markAlertSent($item);
    }

    private function isDuplicateAlert(Item $item): bool
    {
        $cacheKey = "low_stock_alert_{$item->id}";

        return Cache::has($cacheKey);
    }

    private function markAlertSent(Item $item): void
    {
        $cacheKey = "low_stock_alert_{$item->id}";
        $hours = config('notifications.duplicate_window_hours', 24);

        Cache::put($cacheKey, true, now()->addHours($hours));
    }

    private function notifyAdmins(Item $item): void
    {
        $admins = User::role([RoleType::SUPER_ADMIN->value, RoleType::ADMIN->value])->get();

        Notification::send($admins, new LowStockAlert($item, $item->quantity));
    }
}
