<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum NotificationType: string implements HasColor, HasLabel
{
    case LOW_STOCK = 'low_stock';
    case SALE_SUCCESS = 'sale_success';
    case SALE_FAILURE = 'sale_failure';
    case DAILY_REPORT = 'daily_report';
    case SYSTEM_WARNING = 'system_warning';
    case SYSTEM_ERROR = 'system_error';

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->getLabel(), self::cases()),
        );
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW_STOCK => __('Low Stock'),
            self::SALE_SUCCESS => __('Sale Success'),
            self::SALE_FAILURE => __('Sale Failed'),
            self::DAILY_REPORT => __('Daily Report'),
            self::SYSTEM_WARNING => __('System Warning'),
            self::SYSTEM_ERROR => __('System Error'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LOW_STOCK => 'warning',
            self::SALE_SUCCESS => 'success',
            self::SALE_FAILURE => 'danger',
            self::DAILY_REPORT => 'info',
            self::SYSTEM_WARNING => 'warning',
            self::SYSTEM_ERROR => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LOW_STOCK => 'heroicon-o-exclamation-triangle',
            self::SALE_SUCCESS => 'heroicon-o-check-circle',
            self::SALE_FAILURE => 'heroicon-o-x-circle',
            self::DAILY_REPORT => 'heroicon-o-document-chart-bar',
            self::SYSTEM_WARNING => 'heroicon-o-exclamation-circle',
            self::SYSTEM_ERROR => 'heroicon-o-shield-exclamation',
        };
    }
}
