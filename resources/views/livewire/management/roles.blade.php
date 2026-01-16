<div class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8">
                <flux:heading size="xl" class="mb-2">
                    {{ __('Roles & Permissions') }}
                </flux:heading>
                <flux:subheading>
                    {{ __('Manage user roles and their permissions across the system.') }}
                </flux:subheading>
            </div>

            {{-- Info Banner --}}
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                <svg class="size-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 text-sm text-blue-800 dark:text-blue-300">
                    {{ __('System roles (Super Admin, Admin) cannot be deleted. Create custom roles to fit your organization\'s needs.') }}
                </div>
            </div>

            {{-- Table Container --}}
            <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                {{ $this->table }}
            </div>

            {{-- Help Section --}}
            <div class="mt-6 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <div class="flex items-start gap-3">
                    <svg class="size-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <div class="flex-1">
                        <div class="mb-1 text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ __('Role Management Tips') }}
                        </div>
                        <div class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                            <div>• {{ __('Assign roles to users to control their access to different features') }}</div>
                            <div>• {{ __('Super Admin role has access to all features and cannot be modified') }}</div>
                            <div>• {{ __('Create custom roles for specific departments or job functions') }}</div>
                            <div>• {{ __('Review permissions regularly to ensure security') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>

    <x-filament-actions::modals />
</div>
