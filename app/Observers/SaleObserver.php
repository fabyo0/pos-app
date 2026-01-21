<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\RoleType;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\SaleCompleted;
use App\Notifications\SaleFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

final class SaleObserver
{
    public function created(Sale $sale): void
    {
        try {
            $this->notifyManagement(new SaleCompleted($sale));
        } catch (Throwable $e) {
            Log::error('Failed to send sale notification', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);

            $this->notifyManagement(new SaleFailed($sale, $e->getMessage()));
        }
    }

    private function notifyManagement(object $notification): void
    {
        $users = User::role([
            RoleType::SUPER_ADMIN->value,
            RoleType::ADMIN->value,
            RoleType::MANAGER->value,
        ])->get();

        Notification::send($users, $notification);
    }
}
