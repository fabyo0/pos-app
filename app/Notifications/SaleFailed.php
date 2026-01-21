<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use App\Models\Sale;

final class SaleFailed extends BaseNotification
{
    public function __construct(
        private readonly ?Sale $sale,
        private readonly string $reason,
    ) {
        $this->type = NotificationType::SALE_FAILURE;
        $this->priority = NotificationPriority::HIGH;
        $this->title = __('Sale Failed');
        $this->message = $this->sale
            ? __('Sale #:id failed: :reason', [
                'id' => $this->sale->id,
                'reason' => $this->reason,
            ])
            : __('Sale failed: :reason', [
                'reason' => $this->reason,
            ]);

        $this->metadata = [
            'sale_id' => $this->sale?->id,
            'reason' => $this->reason,
            'customer_id' => $this->sale?->customer_id,
            'attempted_total' => $this->sale?->total_amount,
        ];
    }
}
