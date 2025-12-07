<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header with Avatar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:avatar name="{{ $record->name }}" size="lg" />
            <div>
                <flux:heading size="xl">{{ $record->name }}</flux:heading>
                <flux:text class="mt-1">
                    Customer since {{ $record->created_at->format('M d, Y') }}
                </flux:text>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <flux:button wire:navigate href="{{ route('customers.edit', $record) }}" variant="primary" icon="pencil-square">
                Edit Customer
            </flux:button>
        </div>
    </div>

    <flux:separator />

    {{-- Form Content --}}
    {{ $this->form }}

    {{-- Footer Actions --}}
    <flux:separator />

    <div class="flex items-center justify-between">
        <flux:button wire:navigate href="{{ route('customers.index') }}" variant="ghost" icon="arrow-left">
            Back to Customers
        </flux:button>
        <div class="flex items-center gap-3">
            <flux:button wire:navigate  href="{{ route('customers.edit', $record) }}" variant="filled" icon="pencil-square">
                Edit
            </flux:button>
            <flux:button variant="danger" icon="trash" wire:navigate wire:click="delete" wire:confirm="Are you sure you want to delete this customer?">
                Delete
            </flux:button>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
