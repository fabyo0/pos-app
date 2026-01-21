<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum NotificationPriority: string implements HasColor, HasLabel
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => __('Low'),
            self::MEDIUM => __('Medium'),
            self::HIGH => __('High'),
            self::CRITICAL => __('Critical'),
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::CRITICAL => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LOW => 'heroicon-o-minus-circle',
            self::MEDIUM => 'heroicon-o-information-circle',
            self::HIGH => 'heroicon-o-exclamation-triangle',
            self::CRITICAL => 'heroicon-o-fire',
        };
    }

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->getLabel(), self::cases())
        );
    }
}
