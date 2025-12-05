<div class="max-w-5xl mx-auto space-y-6">
    <form wire:submit="save">
        {{-- Header with Avatar --}}
        <div class="mb-6 flex items-center justify-between">
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
                <flux:button href="{{ route('customers.show', $record) }}" variant="ghost" icon="x-mark">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check">
                    Save Changes
                </flux:button>
            </div>
        </div>

        <flux:separator class="my-6" />

        {{-- Form --}}
        {{ $this->form }}

        {{-- Footer Actions (Mobile friendly) --}}
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <flux:button href="{{ route('customers.show', $record) }}" variant="ghost" class="w-full sm:w-auto">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check" class="w-full sm:w-auto">
                Save Changes
            </flux:button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
