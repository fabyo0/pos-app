<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use App\Models\Sale;

final class SaleCompleted extends BaseNotification
{
    public function __construct(
        private readonly Sale $sale,
    ) {
        $this->type = NotificationType::SALE_SUCCESS;
        $this->priority = NotificationPriority::LOW;
        $this->title = __('Sale Completed');
        $this->message = __('Sale #:id completed successfully. Total: :total', [
            'id' => $this->sale->id,
            'total' => number_format((float) $this->sale->total, 2) . ' ₺',
        ]);

        if ($this->sale->customer) {
            $this->message .= ' - ' . __('Customer: :name', [
                    'name' => $this->sale->customer->name,
                ]);
        }

        $this->metadata = [
            'sale_id' => $this->sale->id,
            'total' => $this->sale->total,
            'customer_id' => $this->sale->customer_id,
            'customer_name' => $this->sale->customer?->name,
            'items_count' => $this->sale->salesItems->count(),
            'payment_method_id' => $this->sale->payment_method_id,
        ];
    }
}
