<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
    <flux:main class="p-4 lg:p-6">
        <div class="mx-auto max-w-7xl space-y-6">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                        {{ __('Authentication Logs') }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Monitor login activities and security events') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <span class="relative flex size-2">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-green-500"></span>
                        </span>
                        <span class="text-zinc-600 dark:text-zinc-300">{{ __('Live') }}</span>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Logins Today --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Logins Today') }}</span>
                        <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                        {{ \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::whereDate('login_at', today())->where('login_successful', true)->count() }}
                    </p>
                </div>

                {{-- Failed Attempts --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Failed Today') }}</span>
                        <svg class="size-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                        {{ \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::whereDate('login_at', today())->where('login_successful', false)->count() }}
                    </p>
                </div>

                {{-- Unique Users --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Active Users') }}</span>
                        <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                        {{ \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::whereDate('login_at', today())->distinct('authenticatable_id')->count('authenticatable_id') }}
                    </p>
                </div>

                {{-- Total Logs --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Logs') }}</span>
                        <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                        {{ \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::count() }}
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                {{ $this->table }}
            </div>
        </div>
    </flux:main>
</div>
