<div class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="xl" class="mb-2">
                        {{ __('Roles & Permissions') }}
                    </flux:heading>
                    <flux:subheading>
                        {{ __('Manage user roles and their permissions across the system.') }}
                    </flux:subheading>
                </div>

                <flux:button variant="primary" href="{{ route('management.roles.create') }}" wire:navigate>
                    <svg class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create Role') }}
                </flux:button>
            </div>

            {{-- Search & Stats Bar --}}
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Search --}}
                <div class="relative w-full sm:w-80">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search roles...') }}"
                        class="block w-full rounded-lg border border-zinc-300 bg-white py-2.5 pl-10 pr-4 text-sm text-zinc-900 placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white dark:placeholder-zinc-400"
                    />
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 rounded-lg bg-zinc-100 px-3 py-2 dark:bg-zinc-800">
                        <svg class="size-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $this->roles->count() }} {{ __('Roles') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Banner --}}
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                <svg class="size-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 text-sm text-blue-800 dark:text-blue-300">
                    {{ __('System roles (Super Admin, Admin) cannot be deleted. Create custom roles to fit your organization\'s needs.') }}
                </div>
            </div>

            {{-- Roles Grid --}}
            @if($this->roles->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-50 py-16 dark:border-zinc-700 dark:bg-zinc-900">
                    <svg class="mb-4 size-16 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="mb-2 text-lg font-semibold text-zinc-900 dark:text-white">{{ __('No roles found') }}</h3>
                    <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Get started by creating your first role.') }}</p>
                    <flux:button variant="primary" href="{{ route('management.roles.create') }}" wire:navigate>
                        <svg class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Create Role') }}
                    </flux:button>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($this->roles as $role)
                        <div class="group relative overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition-all hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                            {{-- Color Bar --}}
                            <div class="h-1.5 w-full bg-{{ $role->color ?? 'gray' }}-500"></div>

                            {{-- Card Content --}}
                            <div class="p-6">
                                {{-- Header --}}
                                <div class="mb-4 flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-12 items-center justify-center rounded-xl bg-{{ $role->color ?? 'gray' }}-100 dark:bg-{{ $role->color ?? 'gray' }}-900/30">
                                            <svg class="size-6 text-{{ $role->color ?? 'gray' }}-600 dark:text-{{ $role->color ?? 'gray' }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-zinc-900 dark:text-white">
                                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                            </h3>
                                            @if($role->is_system)
                                                <span class="inline-flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400">
                                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    {{ __('System Role') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Menu --}}
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="sm" class="size-8 p-0">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </flux:button>

                                        <flux:menu>
                                            <flux:menu.item href="{{ route('management.roles.edit', $role) }}" wire:navigate>
                                                <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            @unless($role->is_system)
                                                <flux:menu.separator />
                                                <flux:menu.item
                                                    variant="danger"
                                                    wire:click="mountAction('delete', { role: {{ $role->id }} })"
                                                >
                                                    <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    {{ __('Delete') }}
                                                </flux:menu.item>
                                            @endunless
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>

                                {{-- Description --}}
                                <p class="mb-4 line-clamp-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $role->description ?? 'No description provided.' }}
                                </p>

                                {{-- Stats --}}
                                <div class="mb-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                                        <div class="flex items-center gap-2">
                                            <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Users') }}</span>
                                        </div>
                                        <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-white">
                                            {{ $role->users_count }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                                        <div class="flex items-center gap-2">
                                            <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Permissions') }}</span>
                                        </div>
                                        <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-white">
                                            {{ $role->permissions_count }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Action Button --}}

                                {{-- Action Buttons --}}
                                <div class="flex gap-2">
                                    <flux:button
                                        variant="ghost"
                                        href="{{ route('management.roles.edit', $role) }}"
                                        wire:navigate
                                        class="flex-1 justify-center"
                                    >
                                        <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ __('Manage') }}
                                    </flux:button>

                                    @unless($role->is_system)
                                        <flux:button
                                            variant="danger"
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this role? Users with this role will lose their permissions.') }}"
                                        >
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </flux:button>
                                    @endunless
                                </div>


                            </div>

                            {{-- Hover Edit Indicator --}}
                            <div class="absolute inset-x-0 bottom-0 h-0.5 origin-left scale-x-0 bg-{{ $role->color ?? 'gray' }}-500 transition-transform group-hover:scale-x-100"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Help Section --}}
            <div class="mt-8 rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800/50">
                <div class="flex items-start gap-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                        <svg class="size-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="mb-2 text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ __('Role Management Tips') }}
                        </h3>
                        <div class="grid gap-2 text-sm text-zinc-600 dark:text-zinc-400 sm:grid-cols-2">
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 size-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Assign roles to users to control their access to different features') }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 size-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Super Admin role has access to all features and cannot be modified') }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 size-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Create custom roles for specific departments or job functions') }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 size-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Review permissions regularly to ensure security') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>

    <x-filament-actions::modals />
</div>
