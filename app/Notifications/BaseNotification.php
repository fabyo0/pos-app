<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    protected NotificationType $type;

    protected NotificationPriority $priority = NotificationPriority::MEDIUM;

    protected string $title;

    protected string $message;

    protected array $metadata = [];

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type->value,
            'priority' => $this->priority->value,
            'title' => $this->title,
            'message' => $this->message,
            'metadata' => $this->metadata,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
