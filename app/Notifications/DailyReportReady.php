<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use Carbon\CarbonInterface;

final class DailyReportReady extends BaseNotification
{
    public function __construct(
        private readonly CarbonInterface $reportDate,
        private readonly array $statistics
    ) {
        $this->type = NotificationType::DAILY_REPORT;
        $this->priority = NotificationPriority::LOW;
        $this->title = __('Daily Report Ready');
        $this->message = __('Daily report for :date is ready. Total sales: :total, Revenue: :revenue', [
            'date' => $this->reportDate->format('d M Y'),
            'total' => $this->statistics['total_sales'] ?? 0,
            'revenue' => number_format($this->statistics['total_revenue'] ?? 0, 2) . ' ₺',
        ]);

        $this->metadata = [
            'report_date' => $this->reportDate->toDateString(),
            'total_sales' => $this->statistics['total_sales'] ?? 0,
            'total_revenue' => $this->statistics['total_revenue'] ?? 0,
            'top_items' => $this->statistics['top_items'] ?? [],
            'low_stock_count' => $this->statistics['low_stock_count'] ?? 0,
        ];
    }
}
