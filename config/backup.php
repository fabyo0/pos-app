<?php

declare(strict_types=1);

return [

    'backup' => [

        'name' => env('APP_NAME', 'laravel-backup'),

        'source' => [

            'files' => [
                'include' => [],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
            ],

            'databases' => [
                'mysql',
            ],
        ],

        'database_dump_compressor' => null,

        'database_dump_file_extension' => '',

        'destination' => [
            'filename_prefix' => '',
            'disks' => [
                'local',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',
    ],

    'notifications' => [

        'notifications' => [
            Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['mail'],
        ],

        'notifiable' => Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_MAIL_TO', 'your@example.com'),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => '',
            'avatar_url' => '',
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'laravel-backup'),
            'disks' => ['local'],
            'health_checks' => [
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'defaultStrategy' => [
            'keepAllBackupsForDays' => 7,
            'keepDailyBackupsForDays' => 16,
            'keepWeeklyBackupsForWeeks' => 8,
            'keepMonthlyBackupsForMonths' => 4,
            'keepYearlyBackupsForYears' => 2,
            'deleteOldestBackupsWhenUsingMoreMegabytesThan' => 5000,
        ],
    ],

];
