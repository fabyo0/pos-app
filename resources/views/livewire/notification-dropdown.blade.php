<div wire:poll.30s>
    <flux:dropdown position="bottom" align="end">
        <flux:tooltip :content="__('Notifications')" position="bottom">
            <flux:navbar.item icon="bell" class="relative">
                @if($this->unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white">
                        {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
                    </span>
                @endif
            </flux:navbar.item>
        </flux:tooltip>

        <flux:menu class="w-80">
            <flux:menu.heading class="flex items-center justify-between">
                {{ __('Notifications') }}
                @if($this->unreadCount > 0)
                    <flux:badge size="sm" color="red">{{ $this->unreadCount }} {{ __('new') }}</flux:badge>
                @endif
            </flux:menu.heading>

            <flux:menu.separator />

            @forelse($this->notifications as $notification)
                <div
                    class="group relative px-2 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg mx-1 my-0.5 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}"
                    wire:key="notification-{{ $notification->id }}"
                >
                    <div class="flex items-start gap-3">
                        {{-- Icon --}}
                        <div class="mt-0.5">
                            <flux:icon
                                :name="$this->getIcon($notification->data['type'] ?? '')"
                                class="size-5 {{ $this->getColor($notification->data['type'] ?? '') }}"
                            />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $notification->data['title'] ?? __('Notification') }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if(is_null($notification->read_at))
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                    title="{{ __('Mark as read') }}"
                                >
                                    <flux:icon name="check" class="size-4 text-zinc-500" />
                                </button>
                            @endif
                            <button
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                class="p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                title="{{ __('Delete') }}"
                            >
                                <flux:icon name="x-mark" class="size-4 text-zinc-500" />
                            </button>
                        </div>
                    </div>

                    {{-- Unread indicator --}}
                    @if(is_null($notification->read_at))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-blue-500 rounded-r"></span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <flux:icon name="bell-slash" class="size-8 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No notifications') }}
                    </p>
                </div>
            @endforelse

            @if($this->notifications->isNotEmpty())
                <flux:menu.separator />

                <div class="flex items-center justify-between px-2 py-1">
                    @if($this->unreadCount > 0)
                        <button
                            wire:click="markAllAsRead"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ __('Mark all as read') }}
                        </button>
                    @else
                        <span></span>
                    @endif

                    <a
                        href="{{ route('notifications.index') }}"
                        wire:navigate
                        class="text-xs text-zinc-600 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-300"
                    >
                        {{ __('View all') }}
                    </a>
                </div>
            @endif
        </flux:menu>
    </flux:dropdown>
</div>
