@php
    $ip = $getRecord()->ip_address;
    $location = $this->getLocationFromIp($ip);
    $countryCode = $location?->countryCode ?? null;
@endphp

<div class="flex items-center gap-2">
    @if($countryCode && $countryCode !== 'LOCAL')
        {{-- Country Flag using flagcdn.com --}}
        <img
            src="https://flagcdn.com/24x18/{{ strtolower($countryCode) }}.png"
            srcset="https://flagcdn.com/48x36/{{ strtolower($countryCode) }}.png 2x"
            width="24"
            height="18"
            alt="{{ $location?->countryName ?? 'Unknown' }}"
            class="rounded-sm shadow-sm"
            loading="lazy"
        />
    @elseif($countryCode === 'LOCAL')
        {{-- Local Network Icon --}}
        <div class="flex size-6 items-center justify-center rounded bg-zinc-100 dark:bg-zinc-800">
            <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
            </svg>
        </div>
    @else
        {{-- Unknown Location Icon --}}
        <div class="flex size-6 items-center justify-center rounded bg-zinc-100 dark:bg-zinc-800">
            <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
            </svg>
        </div>
    @endif

    <div class="flex flex-col">
        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
            @if($location?->cityName && $location->cityName !== 'Localhost')
                {{ $location->cityName }}
            @elseif($countryCode === 'LOCAL')
                Local
            @else
                Unknown
            @endif
        </span>
        <span class="text-xs text-zinc-500 dark:text-zinc-400">
            @if($countryCode === 'LOCAL')
                {{ $ip }}
            @elseif($location?->countryName)
                {{ $location->countryName }}
            @else
                {{ $ip }}
            @endif
        </span>
    </div>
</div>
