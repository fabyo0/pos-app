<div class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="xl" class="mb-2">
                        {{ __('Database Backups') }}
                    </flux:heading>
                    <flux:subheading>
                        {{ __('Manage your database backups. Create, download, and delete backups as needed.') }}
                    </flux:subheading>
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-4 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                            {{ $this->getBackups()->count() }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Total Backups') }}
                        </div>
                    </div>
                    <flux:separator vertical class="h-12" />
                    <div class="text-center">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                            {{ $this->getTotalSize() }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Total Size') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Disk Space Warning --}}
            @php
                $diskSpaceWarning = $this->getDiskSpaceWarning();
                $largeBackups = $this->getBackups()->filter(fn($backup) => $backup['size_bytes'] > 100 * 1024 * 1024);
            @endphp

            @if($diskSpaceWarning || $largeBackups->isNotEmpty())
                <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/30">
                    <svg class="size-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1 space-y-2 text-sm text-amber-800 dark:text-amber-300">
                        @if($diskSpaceWarning)
                            <div class="font-semibold">{{ $diskSpaceWarning }}</div>
                        @endif
                        @if($largeBackups->isNotEmpty())
                            <div>
                                {{ __('You have') }} <strong>{{ $largeBackups->count() }}</strong>
                                {{ __('large backup(s) (>100 MB). Consider configuring cloud storage for better management.') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Info Alert --}}
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                <svg class="size-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 text-sm text-blue-800 dark:text-blue-300">
                    {{ __('Backups are stored locally. For production, configure S3 or DigitalOcean Spaces in your') }}
                    <code class="mx-1 rounded bg-blue-100 px-1.5 py-0.5 text-xs dark:bg-blue-900">config/backup.php</code>
                    {{ __('file.') }}
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
                            {{ __('Backup Tips') }}
                        </div>
                        <div class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                            <div>• {{ __('Backups run automatically daily at 2:00 AM') }}</div>
                            <div>• {{ __('Old backups are automatically cleaned based on retention policy') }}</div>
                            <div>• {{ __('Download backups before deleting for safekeeping') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>

    <x-filament-actions::modals />
</div>
