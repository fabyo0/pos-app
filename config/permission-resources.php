<?php

declare(strict_types=1);

return [
    'dashboard' => [
        'label' => 'Dashboard',
        'icon' => 'home',
        'permissions' => ['view'],
    ],

    'customers' => [
        'label' => 'Customers',
        'icon' => 'users',
        'permissions' => ['view', 'create', 'edit', 'delete', 'export', 'import'],
    ],

    'items' => [
        'label' => 'Items',
        'icon' => 'cube',
        'permissions' => ['view', 'create', 'edit', 'delete', 'export', 'import'],
    ],

    'sales' => [
        'label' => 'Sales',
        'icon' => 'shopping-cart',
        'permissions' => ['view', 'create', 'edit', 'delete', 'export', 'refund'],
    ],

    'inventory' => [
        'label' => 'Inventory',
        'icon' => 'queue-list',
        'permissions' => ['view', 'manage'],
    ],

    'users' => [
        'label' => 'Users',
        'icon' => 'user-group',
        'permissions' => ['view', 'create', 'edit', 'delete'],
    ],

    'payment-methods' => [
        'label' => 'Payment Methods',
        'icon' => 'banknotes',
        'permissions' => ['view', 'create', 'edit', 'delete'],
    ],

    'backups' => [
        'label' => 'Backups',
        'icon' => 'circle-stack',
        'permissions' => ['view', 'create', 'delete'],
    ],

    'roles' => [
        'label' => 'Roles & Permissions',
        'icon' => 'shield-check',
        'permissions' => ['view', 'create', 'edit', 'delete'],
    ],
];
