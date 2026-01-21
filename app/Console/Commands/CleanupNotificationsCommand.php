<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CleanupNotificationsCommand extends Command
{
    protected $signature = 'notifications:cleanup {--days= : Number of days to retain}';

    protected $description = 'Delete old read notifications';

    public function handle(): int
    {
        $days = $this->option('days') ?? config('notifications.retention_days', 30);

        $this->info("Cleaning up read notifications older than {$days} days...");

        $deleted = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays((int) $days))
            ->delete();

        $this->info("Deleted {$deleted} old notification(s).");

        return self::SUCCESS;
    }
}
