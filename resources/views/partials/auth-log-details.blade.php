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

        {{-- Location with Flag --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Location</p>
            <div class="mt-2 flex items-center gap-3">
                @if($location && $location->countryCode && $location->countryCode !== 'LOCAL')
                    <img
                        src="https://flagcdn.com/32x24/{{ strtolower($location->countryCode) }}.png"
                        srcset="https://flagcdn.com/64x48/{{ strtolower($location->countryCode) }}.png 2x"
                        width="32"
                        height="24"
                        alt="{{ $location->countryName ?? 'Unknown' }}"
                        class="rounded shadow-sm"
                    />
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $location->cityName ?? 'Unknown City' }}
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $location->regionName ?? '' }}{{ $location->regionName && $location->countryName ? ', ' : '' }}{{ $location->countryName ?? '' }}
                        </p>
                    </div>
                @elseif($location && $location->countryCode === 'LOCAL')
                    <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                        <svg class="size-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-zinc-100">Local Network</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $log->ip_address }}</p>
                    </div>
                @else
                    <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                        <svg class="size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-zinc-100">Unknown</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $log->ip_address }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- IP Address --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">IP Address</p>
            <div class="mt-1 flex items-center gap-2">
                <p class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $log->ip_address ?? 'Unknown' }}
                </p>
                <button
                    onclick="navigator.clipboard.writeText('{{ $log->ip_address }}')"
                    class="rounded p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300"
                    title="Copy IP"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
            @if($location && $location->countryCode !== 'LOCAL' && ($location->latitude ?? false))
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    Lat: {{ $location->latitude }}, Lon: {{ $location->longitude }}
                </p>
            @endif
        </div>

        {{-- Login Time --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Login Time</p>
            <p class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $log->login_at?->format('M d, Y H:i:s') ?? 'N/A' }}
            </p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $log->login_at?->diffForHumans() }}</p>
        </div>
    </div>

    {{-- Device Info --}}
    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
        <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Device & Browser</p>
        <div class="mt-2 flex items-center gap-3">
            @php
                $userAgent = $log->user_agent;
                $os = match(true) {
                    str_contains($userAgent ?? '', 'Windows') => ['Windows', 'heroicon-o-computer-desktop'],
                    str_contains($userAgent ?? '', 'Mac') => ['macOS', 'heroicon-o-computer-desktop'],
                    str_contains($userAgent ?? '', 'iPhone') => ['iPhone', 'heroicon-o-device-phone-mobile'],
                    str_contains($userAgent ?? '', 'iPad') => ['iPad', 'heroicon-o-device-tablet'],
                    str_contains($userAgent ?? '', 'Android') => ['Android', 'heroicon-o-device-phone-mobile'],
                    str_contains($userAgent ?? '', 'Linux') => ['Linux', 'heroicon-o-computer-desktop'],
                    default => ['Unknown', 'heroicon-o-computer-desktop'],
                };
                $browser = match(true) {
                    str_contains($userAgent ?? '', 'Chrome') && !str_contains($userAgent ?? '', 'Edg') => 'Chrome',
                    str_contains($userAgent ?? '', 'Firefox') => 'Firefox',
                    str_contains($userAgent ?? '', 'Safari') && !str_contains($userAgent ?? '', 'Chrome') => 'Safari',
                    str_contains($userAgent ?? '', 'Edg') => 'Edge',
                    str_contains($userAgent ?? '', 'Opera') || str_contains($userAgent ?? '', 'OPR') => 'Opera',
                    default => 'Unknown',
                };
            @endphp
            <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <x-dynamic-component :component="$os[1]" class="size-5 text-zinc-600 dark:text-zinc-400" />
            </div>
            <div>
                <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $os[0] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $browser }}</p>
            </div>
        </div>
    </div>

    {{-- Full User Agent --}}
    <details class="rounded-lg border border-zinc-200 dark:border-zinc-700">
        <summary class="cursor-pointer px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800">
            View Raw User Agent
        </summary>
        <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
            <p class="break-all font-mono text-xs text-zinc-600 dark:text-zinc-400">
                {{ $log->user_agent ?? 'Unknown' }}
            </p>
        </div>
    </details>
</div>
