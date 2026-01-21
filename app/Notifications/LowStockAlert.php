<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use App\Models\Item;

final class LowStockAlert extends BaseNotification
{
    public function __construct(
        private readonly Item $item,
        private readonly int $currentQuantity,
    ) {
        $this->type = NotificationType::LOW_STOCK;
        $this->priority = $this->determinePriority();
        $this->title = __('Low Stock Alert');
        $this->message = __(':item (SKU: :sku) has only :quantity units remaining.', [
            'item' => $this->item->name,
            'sku' => $this->item->sku ?? 'N/A',
            'quantity' => $this->currentQuantity,
        ]);
        $this->metadata = [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'sku' => $this->item->sku,
            'current_quantity' => $this->currentQuantity,
            'threshold' => $this->getThreshold(),
        ];
    }

    private function determinePriority(): NotificationPriority
    {
        if ($this->currentQuantity === 0) {
            return NotificationPriority::CRITICAL;
        }

        if ($this->currentQuantity <= 5) {
            return NotificationPriority::HIGH;
        }

        return NotificationPriority::MEDIUM;
    }

    private function getThreshold(): int
    {
        return $this->item->low_stock_threshold
            ?? config('notifications.low_stock_threshold', 10);
    }
}
