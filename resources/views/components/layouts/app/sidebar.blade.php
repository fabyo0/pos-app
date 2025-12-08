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

    <a wire:navigate href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0">
        <x-app-logo/>
    </a>

    <flux:spacer/>

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
        <flux:dropdown position="bottom" align="end">
            <flux:tooltip :content="__('Notifications')" position="bottom">
                <flux:navbar.item icon="bell" class="relative">
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white">3</span>
                </flux:navbar.item>
            </flux:tooltip>

            <flux:menu class="w-80">
                <flux:menu.heading class="flex items-center justify-between">
                    {{ __('Notifications') }}
                    <flux:badge size="sm" color="red">3 {{ __('new') }}</flux:badge>
                </flux:menu.heading>

                <flux:menu.separator/>

                <flux:menu.item icon="exclamation-triangle" class="text-amber-600 dark:text-amber-400">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ __('Low Stock Alert') }}</span>
                        <span class="text-xs text-zinc-500">{{ __('5 items running low') }}</span>
                    </div>
                </flux:menu.item>

                <flux:menu.item icon="shopping-cart" class="text-green-600 dark:text-green-400">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ __('New Order') }}</span>
                        <span class="text-xs text-zinc-500">{{ __('Order #1234 received') }}</span>
                    </div>
                </flux:menu.item>

                <flux:menu.item icon="user-plus" class="text-blue-600 dark:text-blue-400">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ __('New Customer') }}</span>
                        <span class="text-xs text-zinc-500">{{ __('John Doe registered') }}</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator/>

                <flux:menu.item icon="eye" class="justify-center text-center">
                    {{ __('View all notifications') }}
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

    </flux:navbar>

    <!-- Header User Menu -->
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

            <flux:menu.radio.group>
                <flux:menu.item wire:navigate :href="route('profile.edit')" icon="cog">
                    {{ __('Settings') }}
                </flux:menu.item>
            </flux:menu.radio.group>

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
    <div class="px-3 my-4">
        <flux:button wire:navigate icon="shopping-bag" variant="primary" class="w-full justify-center">
            {{ __('New Sale') }}
        </flux:button>
    </div>
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')" class="grid">
            <flux:navlist.item wire:navigate icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:separator class="my-3"/>

        <flux:navlist.group :heading="__('Management')" class="grid">
            <flux:navlist.item wire:navigate icon="users" :href="route('customers.index')"
                               :current="request()->routeIs('customers.index')">
                {{ __('Customers') }}
            </flux:navlist.item>
            <flux:navlist.item wire:navigate icon="banknotes" :href="route('management.payment-methods')"
                               :current="request()->routeIs('management.payment-methods')">
                {{ __('Payment Methods') }}
            </flux:navlist.item>
            <flux:navlist.item wire:navigate icon="user-group" :href="route('management.users')"
                               :current="request()->routeIs('management.users')">
                {{ __('Users') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:separator class="my-3"/>

        <flux:navlist.group :heading="__('Inventory')" class="grid">
            <flux:navlist.item wire:navigate icon="cube" :href="route('items.index')"
                               :current="request()->routeIs('items.index')">
                {{ __('Items') }}
            </flux:navlist.item>
            <flux:navlist.item wire:navigate icon="queue-list" :href="route('inventories')"
                               :current="request()->routeIs('inventories')">
                {{ __('Inventory') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:separator class="my-3"/>

        <flux:navlist.group :heading="__('Sales')" class="grid">
            <flux:navlist.item wire:navigate icon="chart-bar" :href="route('sales.index')"
                               :current="request()->routeIs('sales.index')">
                {{ __('Sales') }}
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer/>

    <!-- Sidebar Footer -->
    <div class="border-t border-zinc-200 dark:border-zinc-700 p-3">
        <a target="_blank" href="{{ url('https://github.com/fabyo0/pos-app/discussions') }}" class="flex items-center gap-2 text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
            <flux:icon name="question-mark-circle" variant="outline" class="size-4" />
            {{ __('Help & Support') }}
        </a>

        <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-3">
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
