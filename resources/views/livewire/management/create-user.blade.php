<div class="max-w-4xl mx-auto py-8">
    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-4">
        <flux:breadcrumbs.item href="{{ route('management.users') }}" wire:navigate icon="users">
            Users
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Create</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <flux:heading size="xl">Create User</flux:heading>
            <flux:subheading class="mt-1">Add a new user to the system</flux:subheading>
        </div>

        <flux:button
            variant="subtle"
            icon="arrow-left"
            href="{{ route('management.users') }}"
            wire:navigate
        >
            Back
        </flux:button>
    </div>

    <form wire:submit="create">
        {{ $this->form }}

        <flux:separator class="my-6" />

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                <flux:icon.information-circle variant="micro" class="inline -mt-0.5 mr-1" />
                All fields are required unless marked optional
            </flux:text>

            <div class="flex items-center gap-3">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="cancel"
                    kbd="Esc"
                >
                    Cancel
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    icon="user-plus"
                    wire:loading.attr="disabled"
                    wire:target="create"
                >
                    <span wire:loading.remove wire:target="create">Create User</span>
                    <span wire:loading wire:target="create" class="flex items-center gap-2">
                        <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </flux:button>
            </div>
        </div>
    </form>

    {{-- Keyboard shortcut for cancel --}}
    @script
    <script>
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                $wire.cancel();
            }
        });
    </script>
    @endscript
</div>
