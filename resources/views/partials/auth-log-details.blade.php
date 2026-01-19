<div class="space-y-4">
    {{-- User Info --}}
    <div class="flex items-center gap-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
        <div class="flex size-12 items-center justify-center rounded-full bg-zinc-200 text-lg font-semibold text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">
            {{ strtoupper(substr($log->authenticatable?->name ?? 'U', 0, 2)) }}
        </div>
        <div>
            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->authenticatable?->name ?? 'Unknown User' }}
            </p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $log->authenticatable?->email ?? 'No email' }}
            </p>
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Event Type --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Event</p>
            <p class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                @if($log->logout_at)
                    <span class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </span>
                @elseif($log->login_successful)
                    <span class="inline-flex items-center gap-1.5 text-green-600 dark:text-green-400">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Successful Login
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-red-600 dark:text-red-400">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Failed Login
                    </span>
                @endif
            </p>
        </div>

        {{-- IP Address --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">IP Address</p>
            <p class="mt-1 font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->ip_address ?? 'Unknown' }}
            </p>
        </div>

        {{-- Login Time --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Login Time</p>
            <p class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->login_at?->format('M d, Y H:i:s') ?? 'N/A' }}
            </p>
            <p class="text-xs text-zinc-500">{{ $log->login_at?->diffForHumans() }}</p>
        </div>

        {{-- Logout Time --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Logout Time</p>
            <p class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->logout_at?->format('M d, Y H:i:s') ?? 'Still Active' }}
            </p>
            @if($log->logout_at)
                <p class="text-xs text-zinc-500">{{ $log->logout_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    {{-- User Agent --}}
    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
        <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">User Agent</p>
        <p class="mt-1 break-all text-sm text-zinc-700 dark:text-zinc-300">
            {{ $log->user_agent ?? 'Unknown' }}
        </p>
    </div>

    {{-- Location (if available) --}}
    @if($log->location)
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Location</p>
            <p class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->location['city'] ?? '' }}, {{ $log->location['country'] ?? 'Unknown' }}
            </p>
        </div>
    @endif
</div>
