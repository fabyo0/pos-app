<div class="max-w-3xl mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit {{ $data['name'] }}</h1>
        <p class="text-gray-600 dark:text-gray-400">Update product information</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-3 pt-4">
            <x-filament::button type="submit">
                Save Changes
            </x-filament::button>

            <x-filament::button
                tag="a"
                href="{{ route('items.index') }}"
                color="gray"
            >
                Cancel
            </x-filament::button>
        </div>
    </form>
</div>
