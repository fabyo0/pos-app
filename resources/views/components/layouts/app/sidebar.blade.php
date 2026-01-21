<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @filamentStyles
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
<!-- Top Header -->
<flux:header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <a wire:navigate href="{{ route('dashboard') }}"
       class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0">
        <x-app-logo/>
    </a>

    <flux:spacer/>

    {{-- ⚡ Quick Actions --}}
    <flux:dropdown position="bottom" align="end">
        <flux:tooltip :content="__('Quick Actions')" position="bottom">
            <flux:navbar.item icon="bolt" class="cursor-pointer"/>
        </flux:tooltip>

        <flux:menu class="w-56">
            <flux:menu.heading>{{ __('Quick Actions') }}</flux:menu.heading>

            @can('sales.create')
                <flux:menu.item wire:navigate icon="shopping-cart" :href="route('pos.index')">
                    {{ __('New Sale') }}
                </flux:menu.item>
            @endcan

            @can('customers.create')
                <flux:menu.item wire:navigate icon="user-plus" :href="route('customers.create')">
                    {{ __('Add Customer') }}
                </flux:menu.item>
            @endcan

            @can('items.create')
                <flux:menu.item wire:navigate icon="plus-circle" :href="route('items.create')">
                    {{ __('Add Item') }}
                </flux:menu.item>
            @endcan

            <flux:menu.separator />

            @can('sales.view')
                <flux:menu.item wire:navigate icon="chart-bar" :href="route('sales.index')">
                    {{ __('View Sales') }}
                </flux:menu.item>
            @endcan

            @can('inventory.view')
                <flux:menu.item wire:navigate icon="clipboard-document-list" :href="route('inventories')">
                    {{ __('Check Inventory') }}
                </flux:menu.item>
            @endcan
        </flux:menu>
    </flux:dropdown>

    <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse">
        <!-- Search -->
        <flux:tooltip :content="__('Search')" position="bottom">
            <flux:navbar.item icon="magnifying-glass" href="#" :label="__('Search')"/>
        </flux:tooltip>

        <!-- Dark/Light Mode Toggle -->
        <flux:tooltip :content="__('Toggle theme')" position="bottom">
            <flux:navbar.item
                x-data="{ dark: document.documentElement.classList.contains('dark') }"
                x-on:click="dark = !dark; document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                class="cursor-pointer"
            >
                <span x-show="dark" class="flex items-center justify-center">
                    <flux:icon name="sun" variant="outline" class="size-5"/>
                </span>
                <span x-show="!dark" x-cloak class="flex items-center justify-center">
                    <flux:icon name="moon" variant="outline" class="size-5"/>
                </span>
            </flux:navbar.item>
        </flux:tooltip>

        <!-- Notifications -->
        <livewire:notification-dropdown />
    </flux:navbar>

    <!-- User Menu -->
    <flux:dropdown position="top" align="end">
        <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()"/>

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator/>

            <flux:menu.item wire:navigate :href="route('profile.edit')" icon="cog">
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator/>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

<!-- Sidebar -->
<flux:sidebar stashable sticky class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a wire:navigate href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse lg:hidden">
        <x-app-logo/>
    </a>

    {{-- POS Button - Primary Action --}}
    @can('sales.create')
        <div class="px-3 my-4">
            <flux:button wire:navigate icon="shopping-bag" href="{{ route('pos.index') }}" variant="primary" class="w-full justify-center">
                {{ __('New Sale') }}
            </flux:button>
        </div>
    @endcan

    <flux:navlist variant="outline">
        {{-- Overview --}}
        @can('dashboard.view')
            <flux:navlist.group :heading="__('Overview')" expandable :expanded="request()->routeIs('dashboard')">
                <flux:navlist.item
                    wire:navigate
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                >
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                @can('sales.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="presentation-chart-line"
                        :href="route('sales.index')"
                        :current="request()->routeIs('sales.*')"
                    >
                        {{ __('Analytics') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcan

        {{-- Sales & Orders --}}
        @canany(['sales.view', 'sales.create'])
            <flux:navlist.group :heading="__('Sales')" expandable :expanded="request()->routeIs('sales.*', 'pos.*')">
                @can('sales.create')
                    <flux:navlist.item
                        wire:navigate
                        icon="shopping-cart"
                        :href="route('pos.index')"
                        :current="request()->routeIs('pos.*')"
                    >
                        {{ __('Point of Sale') }}
                    </flux:navlist.item>
                @endcan

                @can('sales.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="document-text"
                        :href="route('sales.index')"
                        :current="request()->routeIs('sales.index')"
                    >
                        {{ __('Transactions') }}
                    </flux:navlist.item>
                @endcan

                @can('sales.refund')
                    <flux:navlist.item
                        wire:navigate
                        icon="receipt-refund"
                        href="#"
                    >
                        {{ __('Refunds') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany

        {{-- Inventory --}}
        @canany(['items.view', 'inventory.view'])
            <flux:navlist.group :heading="__('Inventory')" expandable :expanded="request()->routeIs('items.*', 'inventories*')">
                @can('items.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="cube"
                        :href="route('items.index')"
                        :current="request()->routeIs('items.*')"
                    >
                        {{ __('Products') }}
                    </flux:navlist.item>
                @endcan

                @can('inventory.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="archive-box"
                        :href="route('inventories')"
                        :current="request()->routeIs('inventories*')"
                    >
                        {{ __('Stock Levels') }}
                    </flux:navlist.item>
                @endcan

                @can('inventory.adjust')
                    <flux:navlist.item
                        wire:navigate
                        icon="adjustments-horizontal"
                        href="#"
                    >
                        {{ __('Adjustments') }}
                    </flux:navlist.item>
                @endcan

                @can('inventory.transfer')
                    <flux:navlist.item
                        wire:navigate
                        icon="arrows-right-left"
                        href="#"
                    >
                        {{ __('Transfers') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany

        {{-- Customers --}}
        @can('customers.view')
            <flux:navlist.group :heading="__('Customers')" expandable :expanded="request()->routeIs('customers.*')">
                <flux:navlist.item
                    wire:navigate
                    icon="users"
                    :href="route('customers.index')"
                    :current="request()->routeIs('customers.index')"
                >
                    {{ __('All Customers') }}
                </flux:navlist.item>

                @can('customers.create')
                    <flux:navlist.item
                        wire:navigate
                        icon="user-plus"
                        :href="route('customers.create')"
                        :current="request()->routeIs('customers.create')"
                    >
                        {{ __('Add Customer') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcan

        {{-- Finance --}}
        @canany(['payment-methods.view', 'sales.export'])
            <flux:navlist.group :heading="__('Finance')" expandable :expanded="request()->routeIs('management.payment-methods*')">
                @can('payment-methods.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="credit-card"
                        :href="route('management.payment-methods')"
                        :current="request()->routeIs('management.payment-methods*')"
                    >
                        {{ __('Payment Methods') }}
                    </flux:navlist.item>
                @endcan

                @can('sales.export')
                    <flux:navlist.item
                        wire:navigate
                        icon="document-chart-bar"
                        href="#"
                    >
                        {{ __('Reports') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany

        {{-- Team Management --}}
        @canany(['users.view', 'roles.view'])
            <flux:navlist.group :heading="__('Team')" expandable :expanded="request()->routeIs('management.users*', 'management.roles*')">
                @can('users.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="user-group"
                        :href="route('management.users')"
                        :current="request()->routeIs('management.users*')"
                    >
                        {{ __('Users') }}
                    </flux:navlist.item>
                @endcan

                @can('roles.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="shield-check"
                        :href="route('management.roles')"
                        :current="request()->routeIs('management.roles*')"
                    >
                        {{ __('Roles & Permissions') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany

        {{-- System --}}
        @canany(['backups.view', 'settings.view', 'authentication-logs.view'])
            <flux:navlist.group :heading="__('System')" expandable :expanded="request()->routeIs('backups.*', 'settings.*', 'management.authentication-logs*', 'notifications.*')">
                @can('authentication-logs.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="finger-print"
                        :href="route('management.authentication-logs')"
                        :current="request()->routeIs('management.authentication-logs*')"
                    >
                        {{ __('Auth Logs') }}
                    </flux:navlist.item>
                @endcan

                <flux:navlist.item
                    wire:navigate
                    icon="bell"
                    :href="route('notifications.index')"
                    :current="request()->routeIs('notifications.*')"
                >
                    {{ __('Notifications') }}
                </flux:navlist.item>

                @can('backups.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="circle-stack"
                        :href="route('backups.index')"
                        :current="request()->routeIs('backups.*')"
                    >
                        {{ __('Backups') }}
                    </flux:navlist.item>
                @endcan

                @can('settings.view')
                    <flux:navlist.item
                        wire:navigate
                        icon="cog-6-tooth"
                        href="#"
                    >
                        {{ __('Settings') }}
                    </flux:navlist.item>
                @endcan

                <flux:navlist.item
                    icon="document-text"
                    href="{{ url('log-viewer') }}"
                    target="_blank"
                >
                    {{ __('Logs') }}
                </flux:navlist.item>
            </flux:navlist.group>
        @endcanany
    </flux:navlist>

    <flux:spacer/>

    {{-- User Quick Info --}}
    <div class="mx-3 mb-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800">
        <div class="flex items-center gap-3">
            <div class="flex size-9 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                {{ auth()->user()->initials() }}
            </div>
            <div class="flex-1 truncate">
                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                    {{ auth()->user()->name }}
                </p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                    {{ auth()->user()->roles->first()?->name ? ucwords(str_replace('_', ' ', auth()->user()->roles->first()->name)) : 'No Role' }}
                </p>
            </div>
            <flux:dropdown position="top" align="end">
                <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" class="size-7 p-0" />
                <flux:menu>
                    <flux:menu.item wire:navigate :href="route('profile.edit')" icon="user">
                        {{ __('Profile') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-zinc-200 p-3 dark:border-zinc-700">
        <a target="_blank" href="{{ url('https://github.com/fabyo0/pos-app/discussions') }}"
           class="flex items-center gap-2 text-sm text-zinc-500 transition-colors hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
            <flux:icon name="question-mark-circle" variant="outline" class="size-4"/>
            {{ __('Help & Support') }}
        </a>

        <p class="mt-3 text-[10px] text-zinc-400 dark:text-zinc-500">
            {{ config('app.name') }} v1.0.0 · © {{ date('Y') }}
        </p>
    </div>
</flux:sidebar>

{{ $slot }}

@livewire('notifications')
@filamentScripts
@fluxScripts
</body>

</html>
