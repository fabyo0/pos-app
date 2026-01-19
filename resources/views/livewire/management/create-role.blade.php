<div class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <flux:button variant="ghost" href="{{ route('management.roles') }}" wire:navigate class="mb-4">
                    <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Roles') }}
                </flux:button>

                <flux:heading size="xl">{{ __('Create New Role') }}</flux:heading>
                <flux:subheading>{{ __('Define role details and assign permissions to control access.') }}</flux:subheading>
            </div>

            <div class="grid gap-8 lg:grid-cols-12">
                {{-- Left Sidebar: Role Details --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        {{-- Role Information --}}
                        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                        <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <flux:heading size="sm">{{ __('Role Information') }}</flux:heading>
                                        <flux:subheading size="sm">{{ __('Basic details') }}</flux:subheading>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <form wire:submit="save" class="space-y-4">
                                    {{ $this->form }}
                                </form>
                            </div>
                        </div>

                        {{-- Permission Summary --}}
                        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                                <flux:heading size="sm">{{ __('Permission Summary') }}</flux:heading>
                            </div>

                            <div class="p-6 space-y-4">
                                {{-- Total Selected --}}
                                <div class="flex items-center justify-between rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                            <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Selected') }}</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Permissions') }}</p>
                                        </div>
                                    </div>
                                    <flux:badge size="lg" color="blue">{{ count($selectedPermissions) }}</flux:badge>
                                </div>

                                {{-- Resource Breakdown --}}
                                <div class="space-y-2">
                                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('By Resource') }}
                                    </p>
                                    <div class="max-h-48 space-y-1 overflow-y-auto">
                                        @foreach($resources as $resource => $config)
                                            @php
                                                $resourcePermissions = array_filter($selectedPermissions, fn($p) => str_starts_with($p, $resource . '.'));
                                                $count = count($resourcePermissions);
                                            @endphp
                                            @if($count > 0)
                                                <div class="flex items-center justify-between rounded-md px-3 py-2 text-sm {{ $this->isResourceFullySelected($resource) ? 'bg-green-50 dark:bg-green-900/20' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $config['label'] }}</span>
                                                    <flux:badge size="sm" :color="$this->isResourceFullySelected($resource) ? 'green' : 'zinc'">
                                                        {{ $count }}/{{ count($config['permissions']) }}
                                                    </flux:badge>
                                                </div>
                                            @endif
                                        @endforeach

                                        @if(count($selectedPermissions) === 0)
                                            <p class="py-4 text-center text-sm text-zinc-400 dark:text-zinc-500">
                                                {{ __('No permissions selected yet') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                                <div class="flex w-full gap-3">
                                    <flux:button type="button" variant="ghost" wire:click="cancel" class="flex-1">
                                        {{ __('Cancel') }}
                                    </flux:button>
                                    <flux:button type="submit" variant="primary" wire:click="save" class="flex-1">
                                        <svg class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Create Role') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Permission Matrix --}}
                <div class="lg:col-span-8">
                    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        {{-- Header --}}
                        <div class="flex flex-col gap-4 border-b border-zinc-200 px-6 py-4 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                    <svg class="size-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <flux:heading size="sm">{{ __('Permission Matrix') }}</flux:heading>
                                    <flux:subheading size="sm">{{ __('Click to toggle permissions') }}</flux:subheading>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="flex items-center gap-2">
                                <flux:button size="sm" variant="ghost" wire:click="selectAll">
                                    <svg class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Select All') }}
                                </flux:button>
                                <flux:button size="sm" variant="ghost" wire:click="clearAll">
                                    <svg class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('Clear All') }}
                                </flux:button>
                            </div>
                        </div>

                        {{-- Matrix Content --}}
                        <div class="overflow-x-auto">
                            {{-- Action Headers --}}
                            <div class="sticky top-0 z-10 grid border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/80 backdrop-blur-sm" style="grid-template-columns: minmax(200px, 1fr) repeat({{ count($this->getUniqueActions()) }}, minmax(80px, 100px))">
                                <div class="px-6 py-3">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('Resource') }}
                                    </span>
                                </div>
                                @foreach($this->getUniqueActions() as $action => $label)
                                    <div class="flex items-center justify-center px-2 py-3">
                                        <button
                                            type="button"
                                            wire:click="toggleAction('{{ $action }}')"
                                            class="group flex flex-col items-center gap-1 rounded-lg px-3 py-2 transition hover:bg-zinc-200 dark:hover:bg-zinc-700 {{ $this->isActionFullySelected($action) ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}"
                                        >
                                            <span class="text-xs font-semibold uppercase tracking-wider {{ $this->isActionFullySelected($action) ? 'text-blue-700 dark:text-blue-300' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                {{ $label }}
                                            </span>
                                            @if($this->isActionFullySelected($action))
                                                <span class="size-1.5 rounded-full bg-blue-500"></span>
                                            @endif
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Resource Rows --}}
                            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach($resources as $resource => $config)
                                    <div
                                        class="grid transition hover:bg-zinc-50 dark:hover:bg-zinc-800/30 {{ $this->isResourceFullySelected($resource) ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}"
                                        style="grid-template-columns: minmax(200px, 1fr) repeat({{ count($this->getUniqueActions()) }}, minmax(80px, 100px))"
                                    >
                                        {{-- Resource Name --}}
                                        <div class="flex items-center gap-3 px-6 py-4">
                                            <button
                                                type="button"
                                                wire:click="toggleResource('{{ $resource }}')"
                                                class="flex items-center gap-3 rounded-lg px-2 py-1 transition hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                            >
                                                <div class="flex size-9 items-center justify-center rounded-lg {{ $this->isResourceFullySelected($resource) ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                                                    <x-dynamic-component
                                                        :component="$config['icon']"
                                                        class="size-5 {{ $this->isResourceFullySelected($resource) ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }}"
                                                    />
                                                </div>
                                                <div class="text-left">
                                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $config['label'] }}</p>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                        {{ count(array_filter($selectedPermissions, fn($p) => str_starts_with($p, $resource . '.'))) }}/{{ count($config['permissions']) }} {{ __('selected') }}
                                                    </p>
                                                </div>
                                            </button>
                                        </div>

                                        {{-- Permission Checkboxes --}}
                                        @foreach($this->getUniqueActions() as $action => $label)
                                            <div class="flex items-center justify-center px-2 py-4">
                                                @if(in_array($action, $config['permissions']))
                                                    <button
                                                        type="button"
                                                        wire:click="togglePermission('{{ $resource }}.{{ $action }}')"
                                                        class="group relative flex size-8 items-center justify-center rounded-lg transition-all duration-200 {{ in_array("{$resource}.{$action}", $selectedPermissions)
                                                            ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/40'
                                                            : 'bg-zinc-200 text-zinc-400 hover:bg-zinc-300 hover:text-zinc-500 dark:bg-zinc-700 dark:hover:bg-zinc-600' }}"
                                                    >
                                                        @if(in_array("{$resource}.{$action}", $selectedPermissions))
                                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @else
                                                            <span class="size-2 rounded-full bg-current opacity-50 group-hover:opacity-100"></span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <span class="flex size-8 items-center justify-center text-zinc-300 dark:text-zinc-600">
                                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Legend --}}
                        <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                            <div class="flex flex-wrap items-center gap-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex size-6 items-center justify-center rounded-md bg-blue-600 text-white">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Permission granted') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex size-6 items-center justify-center rounded-md bg-zinc-200 dark:bg-zinc-700">
                                        <span class="size-2 rounded-full bg-zinc-400"></span>
                                    </div>
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Click to grant') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex size-6 items-center justify-center text-zinc-300 dark:text-zinc-600">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </div>
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Not available') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</div>
