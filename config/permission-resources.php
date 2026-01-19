<?php

declare(strict_types=1);

return [
    'dashboard' => [
        'label' => 'Dashboard',
        'icon' => 'heroicon-o-home',
        'permissions' => [
            'view',
        ],
    ],

    'customers' => [
        'label' => 'Customers',
        'icon' => 'heroicon-o-users',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
            'export',
        ],
    ],

    'items' => [
        'label' => 'Items',
        'icon' => 'heroicon-o-cube',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
            'export',
        ],
    ],

    'sales' => [
        'label' => 'Sales',
        'icon' => 'heroicon-o-shopping-cart',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
            'export',
            'refund',
        ],
    ],

    'inventory' => [
        'label' => 'Inventory',
        'icon' => 'heroicon-o-archive-box',
        'permissions' => [
            'view',
            'manage',
            'adjust',
            'transfer',
        ],
    ],

    'payment-methods' => [
        'label' => 'Payment Methods',
        'icon' => 'heroicon-o-credit-card',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
        ],
    ],

    'users' => [
        'label' => 'Users',
        'icon' => 'heroicon-o-user-group',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
        ],
    ],

    'roles' => [
        'label' => 'Roles',
        'icon' => 'heroicon-o-shield-check',
        'permissions' => [
            'view',
            'create',
            'edit',
            'delete',
        ],
    ],

    'backups' => [
        'label' => 'Backups',
        'icon' => 'heroicon-o-circle-stack',
        'permissions' => [
            'view',
            'create',
            'delete',
            'download',
        ],
    ],

    'settings' => [
        'label' => 'Settings',
        'icon' => 'heroicon-o-cog-6-tooth',
        'permissions' => [
            'view',
            'edit',
        ],
    ],

    'authentication-logs' => [
        'label' => 'Authentication Logs',
        'icon' => 'heroicon-o-finger-print',
        'permissions' => [
            'view',
            'export',
            'delete',
        ],
    ],
];
