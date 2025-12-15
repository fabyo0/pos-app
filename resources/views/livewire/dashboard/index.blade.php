<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>{{ now()->format('l, F j, Y') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:badge color="emerald" icon="clock">{{ now()->format('H:i') }}</flux:badge>
            <flux:button href="{{ route('pos.index') }}" wire:navigate variant="primary" icon="shopping-cart">
                New Sale
            </flux:button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {{-- Today Sales --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Today's Sales</flux:subheading>
                    <flux:heading size="xl" class="mt-1">
                        ${{ number_format($this->todaySales['total'], 2) }}
                    </flux:heading>
                    <flux:subheading size="sm" class="mt-1">
                        {{ $this->todaySales['count'] }} transactions
                    </flux:subheading>
                </div>
                <div class="size-12 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                    <flux:icon name="banknotes" variant="solid" class="size-6 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        {{-- Today Customers --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Today's Customers</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $this->todayCustomers }}</flux:heading>
                    <flux:subheading size="sm" class="mt-1">{{ $this->totalCustomers }} total</flux:subheading>
                </div>
                <div class="size-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                    <flux:icon name="users" variant="solid" class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Low Stock Items</flux:subheading>
                    <flux:heading size="xl" class="mt-1">{{ $this->lowStockItems }}</flux:heading>
                    <p class="text-xs text-red-500 mt-1">{{ $this->outOfStockItems() }} out of stock</p>
                </div>
                <div class="size-12 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                    <flux:icon name="exclamation-triangle" variant="solid" class="size-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        {{-- Month Sales --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>This Month</flux:subheading>
                    <flux:heading size="xl" class="mt-1">
                        ${{ number_format($this->monthSales['total'], 2) }}
                    </flux:heading>
                    <flux:subheading size="sm" class="mt-1">
                        {{ $this->monthSales['count'] }} sales
                    </flux:subheading>
                </div>
                <div class="size-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                    <flux:icon name="chart-bar" variant="solid" class="size-6 text-indigo-600 dark:text-indigo-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent Sales --}}
        <div class="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <flux:heading size="lg">Recent Sales</flux:heading>
                <flux:badge color="zinc">Last 5</flux:badge>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->recentSales as $sale)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $sale->customer ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-500' }}">
                                {{ strtoupper(substr($sale->customer?->name ?? 'W', 0, 1)) }}
                            </div>
                            <div>
                                <flux:heading size="sm">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</flux:heading>
                                <flux:subheading size="sm">{{ $sale->created_at->diffForHumans() }}</flux:subheading>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            @php
                                $badgeColor = match($sale->paymentMethod?->name) {
                                    'Cash' => 'emerald',
                                    'Card' => 'blue',
                                    'Mobile Money' => 'amber',
                                    'Bank Transfer' => 'purple',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge size="sm" :color="$badgeColor">
                                {{ $sale->paymentMethod?->name ?? '-' }}
                            </flux:badge>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600 dark:text-emerald-400">
                                    ${{ number_format($sale->total, 2) }}
                                </p>
                                <flux:subheading size="sm">{{ $sale->sales_items_count }} items</flux:subheading>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <flux:icon name="shopping-bag" class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
                        <flux:subheading>No sales yet today</flux:subheading>
                        <flux:button href="{{ route('pos.index') }}" wire:navigate variant="primary" size="sm" class="mt-4">
                            Make First Sale
                        </flux:button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Top Selling --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                    <flux:heading size="lg">Top Selling</flux:heading>
                    <flux:badge size="sm" color="indigo">This Month</flux:badge>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->topSellingItems() as $index => $item)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="size-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm text-zinc-900 dark:text-white truncate max-w-[140px]">
                                    {{ $item->name }}
                                </span>
                            </div>
                            <flux:subheading>{{ $item->total_sold }} sold</flux:subheading>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <flux:subheading>No sales data yet</flux:subheading>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Low Stock Alert --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                    <flux:heading size="lg">Low Stock Alert</flux:heading>
                    @if($this->lowStockItems > 0)
                        <flux:badge size="sm" color="amber">{{ $this->lowStockItems }}</flux:badge>
                    @else
                        <flux:badge size="sm" color="emerald">OK</flux:badge>
                    @endif
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700 max-h-64 overflow-y-auto">
                    @forelse($this->lowStockList as $inventory)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <span class="text-sm text-zinc-900 dark:text-white truncate max-w-[140px]">
                                {{ $inventory->item->name }}
                            </span>
                            @php
                                $stockColor = match(true) {
                                    $inventory->quantity <= 0 => 'red',
                                    $inventory->quantity <= 5 => 'amber',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge size="sm" :color="$stockColor">
                                {{ $inventory->quantity }} left
                            </flux:badge>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <flux:icon name="check-circle" class="size-8 text-emerald-500 mx-auto mb-2" />
                            <flux:subheading>All items well stocked</flux:subheading>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
