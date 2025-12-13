<div class="flex h-[calc(100vh-4rem)]">
    {{-- Left Panel - Products --}}
    <div class="flex-1 flex flex-col bg-zinc-100 dark:bg-zinc-900">
        {{-- Top Bar with Categories --}}
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-white">Point of Sale</h1>
                    <p class="text-xs text-zinc-500">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <flux:badge color="emerald" icon="clock">
                        {{ now()->format('H:i') }}
                    </flux:badge>
                    <flux:badge color="zinc">
                        {{ count($this->filteredItems) }} Products
                    </flux:badge>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        icon="magnifying-glass"
                        placeholder="Search by name, SKU, or barcode..."
                        clearable
                        class="bg-zinc-50 dark:bg-zinc-900"
                    />
                </div>
                <flux:button icon="funnel" variant="ghost" />
                <flux:button icon="squares-2x2" variant="ghost" />
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @forelse ($this->filteredItems as $item)
                    <button
                        wire:click="addToCart({{ $item['id'] }})"
                        wire:loading.attr="disabled"
                        wire:target="addToCart({{ $item['id'] }})"
                        class="group relative bg-white dark:bg-zinc-800 rounded-xl p-3 text-left
                               border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400
                               transition-all duration-150 hover:shadow-lg active:scale-[0.98]
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                               dark:focus:ring-offset-zinc-900"
                    >
                        {{-- Stock Indicator --}}
                        <div class="absolute top-2 right-2">
                            @if($item['stock'] <= 5)
                                <span class="flex size-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-red-500"></span>
                                </span>
                            @endif
                        </div>

                        {{-- Product Image --}}
                        <div class="aspect-square bg-gradient-to-br from-zinc-100 to-zinc-200
                                    dark:from-zinc-700 dark:to-zinc-600 rounded-lg mb-3
                                    flex items-center justify-center overflow-hidden
                                    group-hover:from-indigo-50 group-hover:to-indigo-100
                                    dark:group-hover:from-indigo-900/30 dark:group-hover:to-indigo-800/30
                                    transition-colors">
                            <flux:icon name="cube" class="size-8 text-zinc-400 group-hover:text-indigo-500 transition-colors" />
                        </div>

                        {{-- Product Info --}}
                        <div class="space-y-1">
                            <h3 class="font-medium text-sm text-zinc-900 dark:text-white line-clamp-2 leading-tight">
                                {{ $item['name'] }}
                            </h3>
                            <p class="text-[10px] text-zinc-400 font-mono">{{ $item['sku'] }}</p>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-base font-bold text-indigo-600 dark:text-indigo-400">
                                    ${{ number_format($item['price'], 2) }}
                                </span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded
                                    {{ $item['stock'] > 10 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' :
                                       ($item['stock'] > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                       'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400') }}">
                                    {{ $item['stock'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="addToCart({{ $item['id'] }})"
                             class="absolute inset-0 bg-white/80 dark:bg-zinc-800/80 rounded-xl flex items-center justify-center">
                            <flux:icon name="arrow-path" class="size-6 text-indigo-500 animate-spin" />
                        </div>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20">
                        <div class="size-24 bg-zinc-200 dark:bg-zinc-700 rounded-full flex items-center justify-center mb-4">
                            <flux:icon name="magnifying-glass" class="size-12 text-zinc-400" />
                        </div>
                        <p class="text-zinc-600 dark:text-zinc-400 font-medium text-lg">No products found</p>
                        <p class="text-zinc-400 dark:text-zinc-500 text-sm mt-1">Try a different search term</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions Bar --}}
        <div class="bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="ghost" icon="arrow-path">Refresh</flux:button>
                    <flux:button size="sm" variant="ghost" icon="clock">Recent</flux:button>
                </div>
                <div class="flex items-center gap-2 text-xs text-zinc-500">
                    <span class="flex items-center gap-1">
                        <span class="size-2 rounded-full bg-emerald-500"></span> In Stock
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="size-2 rounded-full bg-amber-500"></span> Low
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="size-2 rounded-full bg-red-500"></span> Critical
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel - Cart & Checkout --}}
    <div class="w-[400px] bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 flex flex-col">
        {{-- Cart Header --}}
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                        <flux:icon name="shopping-cart" class="size-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h2 class="font-bold text-zinc-900 dark:text-white">Current Order</h2>
                        <p class="text-xs text-zinc-500">{{ count($this->cart) }} items</p>
                    </div>
                </div>
                @if(count($this->cart) > 0)
                    <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                        Clear
                    </flux:button>
                @endif
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($this->cart as $cartItem)
                <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700/50 hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                    <div class="flex gap-3">
                        {{-- Item Image --}}
                        <div class="size-14 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center shrink-0">
                            <flux:icon name="cube" class="size-6 text-zinc-400" />
                        </div>

                        {{-- Item Details --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-sm text-zinc-900 dark:text-white truncate">
                                {{ $cartItem['name'] }}
                            </h4>
                            <p class="text-xs text-zinc-500 mt-0.5">
                                ${{ number_format($cartItem['price'], 2) }} × {{ $cartItem['quantity'] }}
                            </p>

                            {{-- Quantity Controls --}}
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                                    <button
                                        wire:click="decrementQuantity({{ $cartItem['id'] }})"
                                        class="size-7 flex items-center justify-center text-zinc-600 dark:text-zinc-300
                                               hover:bg-zinc-200 dark:hover:bg-zinc-600 rounded-l-lg transition-colors"
                                    >
                                        <flux:icon name="minus" class="size-3" />
                                    </button>
                                    <span class="w-8 text-center text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $cartItem['quantity'] }}
                                    </span>
                                    <button
                                        wire:click="incrementQuantity({{ $cartItem['id'] }})"
                                        class="size-7 flex items-center justify-center text-zinc-600 dark:text-zinc-300
                                               hover:bg-zinc-200 dark:hover:bg-zinc-600 rounded-r-lg transition-colors"
                                    >
                                        <flux:icon name="plus" class="size-3" />
                                    </button>
                                </div>
                                <button
                                    wire:click="removeCart({{ $cartItem['id'] }})"
                                    class="size-7 flex items-center justify-center text-red-500 hover:bg-red-50
                                           dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                >
                                    <flux:icon name="trash" class="size-3.5" />
                                </button>
                            </div>
                        </div>

                        {{-- Item Total --}}
                        <div class="text-right">
                            <span class="font-bold text-zinc-900 dark:text-white">
                                ${{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full py-12">
                    <div class="size-20 bg-zinc-100 dark:bg-zinc-700 rounded-2xl flex items-center justify-center mb-4">
                        <flux:icon name="shopping-bag" class="size-10 text-zinc-300 dark:text-zinc-500" />
                    </div>
                    <p class="font-medium text-zinc-600 dark:text-zinc-400">No items yet</p>
                    <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Click products to add them</p>
                </div>
            @endforelse
        </div>

        {{-- Checkout Section --}}
        <div class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
            {{-- Customer & Payment --}}

            {{-- Customer & Payment Section --}}
            <div class="p-4 space-y-3 border-b border-zinc-200 dark:border-zinc-700">
                <div class="grid grid-cols-2 gap-2">
                    {{-- Custom Customer Search --}}
                    <div class="relative" x-data="{ open: false }">
                        <div
                            @click="open = !open"
                            class="flex items-center justify-between px-3 py-2 text-sm bg-white dark:bg-zinc-900
                       border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer
                       hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors"
                        >
                <span class="{{ $customerId ? 'text-zinc-900 dark:text-white' : 'text-zinc-400' }}">
                    {{ $customerId ? $customers->firstWhere('id', $customerId)?->name : 'Select customer...' }}
                </span>
                            <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
                        </div>

                        {{-- Dropdown --}}
                        <div
                            x-show="open"
                            x-cloak
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200
                       dark:border-zinc-700 rounded-lg shadow-lg overflow-hidden"
                        >
                            {{-- Search Input --}}
                            <div class="p-2 border-b border-zinc-200 dark:border-zinc-700">
                                <input
                                    wire:model.live.debounce.300ms="customerSearch"
                                    type="text"
                                    placeholder="Search..."
                                    class="w-full px-3 py-1.5 text-sm bg-zinc-50 dark:bg-zinc-900
                               border border-zinc-200 dark:border-zinc-700 rounded-md
                               focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            {{-- Options --}}
                            <div class="max-h-48 overflow-y-auto">
                                <button
                                    type="button"
                                    wire:click="$set('customerId', null)"
                                    @click="open = false"
                                    class="w-full px-3 py-2 text-left text-sm hover:bg-zinc-100
                               dark:hover:bg-zinc-700 flex items-center gap-2"
                                >
                        <span class="size-6 bg-zinc-200 dark:bg-zinc-600 rounded-full
                                     flex items-center justify-center">
                            <flux:icon name="user" class="size-3 text-zinc-500" />
                        </span>
                                    <span class="text-zinc-600 dark:text-zinc-400">Walk-in Customer</span>
                                </button>

                                @forelse($this->filteredCustomers as $customer)
                                    <button
                                        type="button"
                                        wire:click="$set('customerId', {{ $customer->id }})"
                                        @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm hover:bg-zinc-100
                                   dark:hover:bg-zinc-700 flex items-center gap-2
                                   {{ $customerId == $customer->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
                                    >
                            <span class="size-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full
                                         flex items-center justify-center text-[10px] font-bold
                                         text-indigo-600 dark:text-indigo-400">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-900 dark:text-white truncate">
                                                {{ $customer->name }}
                                            </p>
                                            @if($customer->phone)
                                                <p class="text-[10px] text-zinc-500 truncate">{{ $customer->phone }}</p>
                                            @endif
                                        </div>
                                        @if($customerId == $customer->id)
                                            <flux:icon name="check" class="size-4 text-indigo-600 dark:text-indigo-400" />
                                        @endif
                                    </button>
                                @empty
                                    @if($customerSearch)
                                        <p class="px-3 py-4 text-sm text-zinc-500 text-center">No customers found</p>
                                    @endif
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Custom Select --}}
                    <div class="relative" x-data="{ open: false }">
                        <div
                            @click="open = !open"
                            class="flex items-center justify-between px-3 py-2 text-sm bg-white dark:bg-zinc-900
               border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer
               hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors"
                        >
                            <div class="flex items-center gap-2">
                                @if($paymentMethodId)
                                    @php $selectedMethod = $paymentMethods->firstWhere('id', $paymentMethodId); @endphp
                                    <flux:icon
                                        name="{{ match($selectedMethod?->name) {
                        'Cash' => 'banknotes',
                        'Card' => 'credit-card',
                        'Mobile Money' => 'device-phone-mobile',
                        'Bank Transfer' => 'building-library',
                        default => 'currency-dollar'
                    } }}"
                                        class="size-4 text-zinc-600 dark:text-zinc-300"
                                    />
                                    <span class="text-zinc-900 dark:text-white">{{ $selectedMethod?->name }}</span>
                                @else
                                    <flux:icon name="credit-card" class="size-4 text-zinc-400" />
                                    <span class="text-zinc-400">Payment Method</span>
                                @endif
                            </div>
                            <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
                        </div>

                        {{-- Dropdown --}}
                        <div
                            x-show="open"
                            x-cloak
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200
               dark:border-zinc-700 rounded-lg shadow-lg overflow-hidden"
                        >
                            @foreach($paymentMethods as $method)
                                @php
                                    $icon = match($method->name) {
                                        'Cash' => 'banknotes',
                                        'Card' => 'credit-card',
                                        'Mobile Money' => 'device-phone-mobile',
                                        'Bank Transfer' => 'building-library',
                                        default => 'currency-dollar'
                                    };
                                    $color = match($method->name) {
                                        'Cash' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/50',
                                        'Card' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/50',
                                        'Mobile Money' => 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/50',
                                        'Bank Transfer' => 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/50',
                                        default => 'text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-900/50'
                                    };
                                @endphp
                                <button
                                    type="button"
                                    wire:click="$set('paymentMethodId', {{ $method->id }})"
                                    @click="open = false"
                                    class="w-full px-3 py-2.5 text-left text-sm hover:bg-zinc-100
                       dark:hover:bg-zinc-700 flex items-center gap-3
                       {{ $paymentMethodId == $method->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
                                >
                <span class="size-8 rounded-lg flex items-center justify-center {{ $color }}">
                    <flux:icon name="{{ $icon }}" class="size-4" />
                </span>
                                    <span class="flex-1 font-medium text-zinc-900 dark:text-white">
                    {{ $method->name }}
                </span>
                                    @if($paymentMethodId == $method->id)
                                        <flux:icon name="check" class="size-4 text-indigo-600 dark:text-indigo-400" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Totals --}}
            <div class="p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Subtotal</span>
                    <span class="text-zinc-900 dark:text-white">${{ number_format($this->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Tax (15%)</span>
                    <span class="text-zinc-900 dark:text-white">${{ number_format($this->tax, 2) }}</span>
                </div>


                {{-- Discount Section --}}
                @if(count($this->cart) > 0)
                    @php $hasDiscount = $this->discountAmount > 0; @endphp
                    <div x-data="{ showDiscount: {{ $hasDiscount ? 'true' : 'false' }} }">
                        <template x-if="!showDiscount">
                            <button
                                @click="showDiscount = true"
                                type="button"
                                class="w-full flex items-center justify-center gap-2 p-2.5
                       border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg
                       text-zinc-500 dark:text-zinc-400 hover:border-red-400 hover:text-red-500
                       dark:hover:border-red-500 dark:hover:text-red-400 transition-colors"
                            >
                                <flux:icon name="tag" class="size-4" />
                                <span class="text-sm font-medium">Add Discount</span>
                            </button>
                        </template>

                        <template x-if="showDiscount">
                            <div class="flex items-center gap-2 p-3 bg-gradient-to-r from-red-50 to-orange-50
                        dark:from-red-900/20 dark:to-orange-900/20 rounded-lg
                        border border-red-200 dark:border-red-800/50">
                                <div class="size-8 bg-red-500 rounded-lg flex items-center justify-center shrink-0">
                                    <flux:icon name="tag" class="size-4 text-white" />
                                </div>
                                <div class="flex-1">
                                    <label class="text-[10px] uppercase tracking-wider text-red-500 font-semibold">
                                        Discount Applied
                                    </label>
                                    <div class="flex items-center">
                                        <span class="text-red-500 text-xl font-bold mr-1">-$</span>
                                        <input
                                            wire:model.live.debounce.300ms="discountAmount"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="w-full text-xl font-bold bg-transparent border-0 p-0
                                   text-red-600 dark:text-red-400 focus:ring-0"
                                        >
                                    </div>
                                </div>
                                <button
                                    wire:click="clearDiscount"
                                    @click="showDiscount = false"
                                    type="button"
                                    class="size-8 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center
                           hover:bg-red-200 dark:hover:bg-red-800/50 transition-colors"
                                >
                                    <flux:icon name="trash" class="size-4 text-red-600 dark:text-red-400" />
                                </button>
                            </div>
                        </template>
                    </div>
                @endif


                {{-- Total --}}
                <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-zinc-700">
                    <span class="font-semibold text-zinc-900 dark:text-white">Total</span>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        ${{ number_format($this->total, 2) }}
                    </span>
                </div>
            </div>

            {{-- Payment Amount --}}
            <div class="px-4 pb-4">
                <div class="bg-white dark:bg-zinc-700 rounded-xl p-3 mb-3">
                    <label class="text-xs text-zinc-500 dark:text-zinc-400 block mb-1">Amount Received</label>
                    <div class="flex items-center">
                        <span class="text-xl text-zinc-400 mr-2">$</span>
                        <input
                            wire:model.live.blur="paidAmount"
                            type="number"
                            min="0"
                            class="flex-1 text-2xl font-bold bg-transparent border-0 p-0
                                   text-zinc-900 dark:text-white focus:ring-0"
                            placeholder="0.00"
                        >
                    </div>
                </div>

                {{-- Change --}}
                @if($this->paidAmount > 0)
                    <div class="flex justify-between items-center p-3 rounded-xl mb-3
                                {{ $this->change >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                        <span class="font-medium {{ $this->change >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                            {{ $this->change >= 0 ? 'Change' : 'Amount Due' }}
                        </span>
                        <span class="text-xl font-bold {{ $this->change >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            ${{ number_format(abs($this->change), 2) }}
                        </span>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-2">
                    <flux:button variant="ghost" icon="document-text" class="justify-center">
                        Hold Order
                    </flux:button>
                    <flux:button
                        wire:click="checkout"
                        variant="primary"
                        icon="check-circle"
                        class="justify-center bg-indigo-600 hover:bg-indigo-700"
                        :disabled="count($this->cart) === 0 || !$this->paymentMethodId"
                    >
                        Pay Now
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
