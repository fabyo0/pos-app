<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Default threshold for low stock alerts. Individual items can override
    | this value using the low_stock_threshold column.
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),

    /*
    |--------------------------------------------------------------------------
    | Notification Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep read notifications before cleanup.
    |
    */
    'retention_days' => env('NOTIFICATION_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Duplicate Prevention Window
    |--------------------------------------------------------------------------
    |
    | Time window (in hours) to prevent duplicate low stock alerts
    | for the same item.
    |
    */
    'duplicate_window_hours' => env('NOTIFICATION_DUPLICATE_WINDOW', 24),

    /*
    |--------------------------------------------------------------------------
    | Daily Report Time
    |--------------------------------------------------------------------------
    |
    | Time to generate and send daily reports (24-hour format).
    |
    */
    'daily_report_time' => env('DAILY_REPORT_TIME', '08:00'),

    /*
    |--------------------------------------------------------------------------
    | Polling Interval
    |--------------------------------------------------------------------------
    |
    | Interval (in seconds) for polling new notifications in the UI.
    |
    */
    'polling_interval' => env('NOTIFICATION_POLLING_INTERVAL', 30),
];
