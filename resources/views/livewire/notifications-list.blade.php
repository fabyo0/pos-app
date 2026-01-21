<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
            {{ __('Notifications') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('You have :unread unread notifications out of :total total.', ['unread' => $unreadCount, 'total' => $totalCount]) }}
        </p>
    </div>

    {{-- Filters & Actions --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Status Filter --}}
            <select
                wire:model.live="filter"
                class="rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"
            >
                <option value="all">{{ __('All') }}</option>
                <option value="unread">{{ __('Unread') }}</option>
                <option value="read">{{ __('Read') }}</option>
            </select>

            {{-- Type Filter --}}
            <select
                wire:model.live="type"
                class="rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"
            >
                <option value="">{{ __('All Types') }}</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Priority Filter --}}
            <select
                wire:model.live="priority"
                class="rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"
            >
                <option value="">{{ __('All Priorities') }}</option>
                @foreach($priorities as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            @if($filter !== 'all' || $type || $priority)
                <button
                    wire:click="clearFilters"
                    class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    {{ __('Clear filters') }}
                </button>
            @endif
        </div>

        {{-- Bulk Actions --}}
        <div class="flex items-center gap-2">
            @if(count($selected) > 0)
                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ count($selected) }} {{ __('selected') }}
                </span>
                <button
                    wire:click="markSelectedAsRead"
                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    {{ __('Mark as Read') }}
                </button>
                <button
                    wire:click="deleteSelected"
                    wire:confirm="{{ __('Are you sure you want to delete selected notifications?') }}"
                    class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                >
                    {{ __('Delete') }}
                </button>
            @else
                @if($unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        {{ __('Mark All as Read') }}
                    </button>
                @endif
                <button
                    wire:click="deleteAllRead"
                    wire:confirm="{{ __('Are you sure you want to delete all read notifications?') }}"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                >
                    {{ __('Delete Read') }}
                </button>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        {{-- Select All Header --}}
        @if($notifications->isNotEmpty())
            <div class="flex items-center gap-3 border-b border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <input
                    type="checkbox"
                    wire:model.live="selectAll"
                    class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700"
                />
                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Select all') }}
                </span>
            </div>
        @endif

        {{-- Notification Items --}}
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($notifications as $notification)
                <div
                    wire:key="notification-{{ $notification->id }}"
                    class="group relative flex items-start gap-4 px-4 py-4 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-700/50 {{ is_null($notification->read_at) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}"
                >
                    {{-- Checkbox --}}
                    <input
                        type="checkbox"
                        wire:model.live="selected"
                        value="{{ $notification->id }}"
                        class="mt-1 size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700"
                    />

                    {{-- Icon --}}
                    <div class="mt-0.5 flex-shrink-0">
                        <flux:icon
                            :name="$this->getIcon($notification->data['type'] ?? '')"
                            class="size-6 {{ $this->getColor($notification->data['type'] ?? '') }}"
                        />
                    </div>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">
                                    {{ $notification->data['title'] ?? __('Notification') }}
                                </p>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            </div>

                            {{-- Priority Badge --}}
                            @if(isset($notification->data['priority']))
                                <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $this->getPriorityColor($notification->data['priority']) }}">
                                    {{ ucfirst($notification->data['priority']) }}
                                </span>
                            @endif
                        </div>

                        {{-- Meta --}}
                        <div class="mt-2 flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                            @if(isset($notification->data['type']))
                                <span class="rounded bg-zinc-100 px-1.5 py-0.5 dark:bg-zinc-700">
                                    {{ str_replace('_', ' ', ucfirst($notification->data['type'])) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        @if(is_null($notification->read_at))
                            <button
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-700 dark:hover:bg-zinc-600 dark:hover:text-zinc-300"
                                title="{{ __('Mark as read') }}"
                            >
                                <flux:icon name="check" class="size-4" />
                            </button>
                        @else
                            <button
                                wire:click="markAsUnread('{{ $notification->id }}')"
                                class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-700 dark:hover:bg-zinc-600 dark:hover:text-zinc-300"
                                title="{{ __('Mark as unread') }}"
                            >
                                <flux:icon name="envelope" class="size-4" />
                            </button>
                        @endif
                        <button
                            wire:click="deleteNotification('{{ $notification->id }}')"
                            wire:confirm="{{ __('Are you sure you want to delete this notification?') }}"
                            class="rounded-lg p-2 text-zinc-500 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/50 dark:hover:text-red-400"
                            title="{{ __('Delete') }}"
                        >
                            <flux:icon name="trash" class="size-4" />
                        </button>
                    </div>

                    {{-- Unread Indicator --}}
                    @if(is_null($notification->read_at))
                        <span class="absolute left-0 top-1/2 h-8 w-1 -translate-y-1/2 rounded-r bg-indigo-500"></span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <flux:icon name="bell-slash" class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                    <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-white">
                        {{ __('No notifications') }}
                    </h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('You\'re all caught up! Check back later for new notifications.') }}
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
