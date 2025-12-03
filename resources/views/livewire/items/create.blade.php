<div class="max-w-3xl mx-auto py-8 px-4">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('items.index') }}"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Create Item
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Add a new item to your inventory
                </p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="create">
        {{ $this->form }}

        {{-- Actions --}}
        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-filament::button
                tag="a"
                href="{{ route('items.index') }}"
                color="gray"
                icon="heroicon-o-x-mark"
            >
                Cancel
            </x-filament::button>

            <x-filament::button
                type="submit"
                icon="heroicon-o-plus"
            >
                Create Item
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
