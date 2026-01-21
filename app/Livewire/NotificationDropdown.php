<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\NotificationType;
use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class NotificationDropdown extends Component
{
    public function mount(): void
    {
        // Initial load handled by computed property
    }

    #[Computed]
    public function notifications(): Collection
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            unset($this->notifications, $this->unreadCount);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        unset($this->notifications, $this->unreadCount);
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
            unset($this->notifications, $this->unreadCount);
        }
    }

    public function getIcon(string $type): string
    {
        return match ($type) {
            NotificationType::LOW_STOCK->value => 'exclamation-triangle',
            NotificationType::SALE_SUCCESS->value => 'shopping-cart',
            NotificationType::SALE_FAILURE->value => 'x-circle',
            NotificationType::DAILY_REPORT->value => 'document-chart-bar',
            NotificationType::SYSTEM_WARNING->value => 'exclamation-circle',
            NotificationType::SYSTEM_ERROR->value => 'shield-exclamation',
            default => 'bell',
        };
    }

    public function getColor(string $type): string
    {
        return match ($type) {
            NotificationType::LOW_STOCK->value => 'text-amber-600 dark:text-amber-400',
            NotificationType::SALE_SUCCESS->value => 'text-green-600 dark:text-green-400',
            NotificationType::SALE_FAILURE->value => 'text-red-600 dark:text-red-400',
            NotificationType::DAILY_REPORT->value => 'text-blue-600 dark:text-blue-400',
            NotificationType::SYSTEM_WARNING->value => 'text-amber-600 dark:text-amber-400',
            NotificationType::SYSTEM_ERROR->value => 'text-red-600 dark:text-red-400',
            default => 'text-zinc-600 dark:text-zinc-400',
        };
    }

    public function render(): View
    {
        return view('livewire.notification-dropdown');
    }
}
