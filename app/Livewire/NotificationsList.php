<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class NotificationsList extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'all'; // all, unread, read

    #[Url]
    public string $type = '';

    #[Url]
    public string $priority = '';

    public array $selected = [];

    public bool $selectAll = false;

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = $this->getNotificationsQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
        }
    }

    public function markAsUnread(string $notificationId): void
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->update(['read_at' => null]);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->reset('selected', 'selectAll');
    }

    public function markSelectedAsRead(): void
    {
        if (empty($this->selected)) {
            return;
        }

        DatabaseNotification::whereIn('id', $this->selected)
            ->where('notifiable_id', auth()->id())
            ->update(['read_at' => now()]);

        $this->reset('selected', 'selectAll');
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
        }
    }

    public function deleteSelected(): void
    {
        if (empty($this->selected)) {
            return;
        }

        DatabaseNotification::whereIn('id', $this->selected)
            ->where('notifiable_id', auth()->id())
            ->delete();

        $this->reset('selected', 'selectAll');
    }

    public function deleteAllRead(): void
    {
        auth()->user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        $this->reset('selected', 'selectAll');
    }

    public function clearFilters(): void
    {
        $this->reset('filter', 'type', 'priority');
        $this->resetPage();
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

    public function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            NotificationPriority::LOW->value => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            NotificationPriority::MEDIUM->value => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            NotificationPriority::HIGH->value => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
            NotificationPriority::CRITICAL->value => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render(): View
    {
        return view('livewire.notifications-list', [
            'notifications' => $this->getNotificationsQuery()->paginate(15),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
            'totalCount' => auth()->user()->notifications()->count(),
            'types' => NotificationType::toArray(),
            'priorities' => NotificationPriority::toArray(),
        ]);
    }

    private function getNotificationsQuery()
    {
        $query = auth()->user()->notifications();

        // Filter by read status
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Filter by type
        if ($this->type) {
            $query->where('data->type', $this->type);
        }

        // Filter by priority
        if ($this->priority) {
            $query->where('data->priority', $this->priority);
        }

        return $query->latest();
    }
}
