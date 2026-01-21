<?php

declare(strict_types=1);

return [
    // The database table name
    // You can change this if the database keys get too long for your driver
    'table_name' => 'authentication_log',

    // The database connection where the authentication_log table resides. Leave empty to use the default
    'db_connection' => null,

    // The events the package listens for to log
    'events' => [
        'login' => Illuminate\Auth\Events\Login::class,
        'failed' => Illuminate\Auth\Events\Failed::class,
        'logout' => Illuminate\Auth\Events\Logout::class,
        'other-device-logout' => Illuminate\Auth\Events\OtherDeviceLogout::class,
    ],

    'listeners' => [
        'login' => Rappasoft\LaravelAuthenticationLog\Listeners\LoginListener::class,
        'failed' => Rappasoft\LaravelAuthenticationLog\Listeners\FailedLoginListener::class,
        'logout' => Rappasoft\LaravelAuthenticationLog\Listeners\LogoutListener::class,
        'other-device-logout' => Rappasoft\LaravelAuthenticationLog\Listeners\OtherDeviceLogoutListener::class,
    ],

    'notifications' => [
        'new-device' => [
            // Send the NewDevice notification
            'enabled' => env('NEW_DEVICE_NOTIFICATION', true),

            // Use torann/geoip to attempt to get a location
            // Defaults to false if geoip is not installed
            'location' => function_exists('geoip'),

            // The Notification class to send
            'template' => Rappasoft\LaravelAuthenticationLog\Notifications\NewDevice::class,

            // Rate limiting for notifications
            'rate_limit' => env('NEW_DEVICE_NOTIFICATION_RATE_LIMIT', 3),
            'rate_limit_decay' => env('NEW_DEVICE_NOTIFICATION_RATE_LIMIT_DECAY', 60),

            // Number of minutes after which a user is no longer considered "new"
            // Users connecting from multiple devices/locations shortly after registration won't trigger notifications
            'new_user_threshold_minutes' => env('NEW_DEVICE_NEW_USER_THRESHOLD_MINUTES', 1),
        ],
        'failed-login' => [
            // Send the FailedLogin notification
            'enabled' => env('FAILED_LOGIN_NOTIFICATION', false),

            // Use torann/geoip to attempt to get a location
            // Defaults to false if geoip is not installed
            'location' => function_exists('geoip'),

            // The Notification class to send
            'template' => Rappasoft\LaravelAuthenticationLog\Notifications\FailedLogin::class,

            // Rate limiting for notifications
            'rate_limit' => env('FAILED_LOGIN_NOTIFICATION_RATE_LIMIT', 5),
            'rate_limit_decay' => env('FAILED_LOGIN_NOTIFICATION_RATE_LIMIT_DECAY', 60),
        ],
        'suspicious-activity' => [
            // Send the SuspiciousActivity notification
            'enabled' => env('SUSPICIOUS_ACTIVITY_NOTIFICATION', false),

            // Use torann/geoip to attempt to get a location
            // Defaults to false if geoip is not installed
            'location' => function_exists('geoip'),

            // The Notification class to send
            'template' => Rappasoft\LaravelAuthenticationLog\Notifications\SuspiciousActivity::class,

            // Rate limiting for notifications
            'rate_limit' => env('SUSPICIOUS_ACTIVITY_NOTIFICATION_RATE_LIMIT', 3),
            'rate_limit_decay' => env('SUSPICIOUS_ACTIVITY_NOTIFICATION_RATE_LIMIT_DECAY', 60),
        ],
    ],

    // Suspicious activity detection
    'suspicious' => [
        // Threshold for failed login attempts to be considered suspicious
        'failed_login_threshold' => env('AUTH_LOG_SUSPICIOUS_FAILED_THRESHOLD', 5),

        // Check for unusual login times
        'check_unusual_times' => env('AUTH_LOG_CHECK_UNUSUAL_TIMES', false),

        // Usual login hours (0-23)
        'usual_hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17],
    ],

    // Webhook configuration
    'webhooks' => [
        // [
        //     'url' => 'https://example.com/webhook',
        //     'events' => ['login', 'failed', 'new_device', 'suspicious'],
        //     'headers' => [
        //         'Authorization' => 'Bearer your-token',
        //     ],
        // ],
    ],

    // Webhook settings
    'webhook_settings' => [
        'log_failures' => env('AUTH_LOG_WEBHOOK_LOG_FAILURES', true),
        'timeout' => env('AUTH_LOG_WEBHOOK_TIMEOUT', 10),
    ],

    // When the clean-up command is run, delete old logs greater than `purge` days
    // Don't schedule the clean-up command if you want to keep logs forever.
    'purge' => 365,

    // Prevent session restorations from being logged as new logins
    // When Laravel restores a session (e.g., page refresh, remember me cookie),
    // it fires the Login event. This setting prevents those from creating duplicate log entries.
    'prevent_session_restoration_logging' => env('AUTH_LOG_PREVENT_SESSION_RESTORATION', true),

    // Time window (in minutes) to consider a login as a session restoration
    // If an active session exists for the same device within this window, update it instead of creating a new entry
    'session_restoration_window_minutes' => env('AUTH_LOG_SESSION_RESTORATION_WINDOW', 5),

    // If you are behind an CDN proxy, set 'behind_cdn.http_header_field' to the corresponding http header field of your cdn
    // For cloudflare you can have look at: https://developers.cloudflare.com/fundamentals/get-started/reference/http-request-headers/
    //    'behind_cdn' => [
    //        'http_header_field' => 'HTTP_CF_CONNECTING_IP' // used by Cloudflare
    //    ],

    // If you are not a cdn user, use false
    'behind_cdn' => false,
];
