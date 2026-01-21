<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;

final class SystemError extends BaseNotification
{
    public function __construct(
        string $message,
        array $context = [],
    ) {
        $this->type = NotificationType::SYSTEM_ERROR;
        $this->priority = NotificationPriority::CRITICAL;
        $this->title = __('System Error');
        $this->message = $message;
        $this->metadata = [
            'context' => $context,
            'server_time' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }
}
