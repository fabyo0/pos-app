<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;

final class SystemWarning extends BaseNotification
{
    public function __construct(
        string $message,
        array $context = [],
        NotificationPriority $priority = NotificationPriority::MEDIUM
    ) {
        $this->type = NotificationType::SYSTEM_WARNING;
        $this->priority = $priority;
        $this->title = __('System Warning');
        $this->message = $message;
        $this->metadata = [
            'context' => $context,
            'server_time' => now()->toIso8601String(),
        ];
    }
}
