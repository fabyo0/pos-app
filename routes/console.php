<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Database Backup Schedule
Schedule::command('backup:clean')
    ->daily()
    ->at('01:00')
    ->timezone('Europe/Istanbul')
    ->emailOutputOnFailure(env('BACKUP_MAIL_TO'))
    ->appendOutputTo(storage_path('logs/backup-clean.log'));

Schedule::command('backup:run --only-db')
    ->daily()
    ->at('02:00')
    ->timezone('Europe/Istanbul')
    ->emailOutputOnFailure(env('BACKUP_MAIL_TO'))
    ->appendOutputTo(storage_path('logs/backup-run.log'));
