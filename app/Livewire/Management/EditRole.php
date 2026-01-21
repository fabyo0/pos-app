<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Permission\Models\Role;

final class EditRole extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Locked]
    public Role $role;

    public ?array $data = [];

    public array $selectedPermissions = [];

    public array $resources = [];

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->resources = config('permission-resources');
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        // Data'yı direkt olarak set et
        $this->data = [
            'name' => $role->name,
            'description' => $role->description,
            'color' => $role->color ?? 'blue',
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Details')
                    ->description('Basic information about the role')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Role Name')
                                    ->required()
                                    ->unique(Role::class, 'name', ignorable: $this->role)
                                    ->maxLength(255)
                                    ->placeholder('e.g., warehouse_manager')
                                    ->helperText($this->role->is_system ? 'System role names cannot be changed' : 'Use lowercase with underscores')
                                    ->disabled((bool) $this->role->is_system)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (TextInput $component, $state): void {
                                        if ( ! $this->role->is_system) {
                                            $component->state(str($state)->lower()->replace(' ', '_')->toString());
                                        }
                                    }),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(500)
                                    ->rows(2)
                                    ->placeholder('Brief description of this role'),

                                Select::make('color')
                                    ->label('Badge Color')
                                    ->options([
                                        'gray' => 'Gray',
                                        'red' => 'Red',
                                        'orange' => 'Orange',
                                        'yellow' => 'Yellow',
                                        'green' => 'Green',
                                        'blue' => 'Blue',
                                        'indigo' => 'Indigo',
                                        'purple' => 'Purple',
                                        'pink' => 'Pink',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function togglePermission(string $permission): void
    {
        if (in_array($permission, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permission]));
        } else {
            $this->selectedPermissions[] = $permission;
        }
    }

    public function toggleResource(string $resource): void
    {
        $resourcePermissions = array_map(
            fn($action) => "{$resource}.{$action}",
            $this->resources[$resource]['permissions'],
        );

        $allSelected = $this->isResourceFullySelected($resource);

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $resourcePermissions));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $resourcePermissions)));
        }
    }

    public function toggleAction(string $action): void
    {
        $actionPermissions = [];

        foreach ($this->resources as $resource => $config) {
            if (in_array($action, $config['permissions'])) {
                $actionPermissions[] = "{$resource}.{$action}";
            }
        }

        $allSelected = count(array_intersect($actionPermissions, $this->selectedPermissions)) === count($actionPermissions);

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $actionPermissions));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $actionPermissions)));
        }
    }

    public function selectAll(): void
    {
        $allPermissions = [];

        foreach ($this->resources as $resource => $config) {
            foreach ($config['permissions'] as $action) {
                $allPermissions[] = "{$resource}.{$action}";
            }
        }

        if (count($this->selectedPermissions) === count($allPermissions)) {
            $this->selectedPermissions = [];
        } else {
            $this->selectedPermissions = $allPermissions;
        }
    }

    public function clearAll(): void
    {
        $this->selectedPermissions = [];
    }

    public function isResourceFullySelected(string $resource): bool
    {
        $resourcePermissions = array_map(
            fn($action) => "{$resource}.{$action}",
            $this->resources[$resource]['permissions'],
        );

        return count(array_intersect($resourcePermissions, $this->selectedPermissions)) === count($resourcePermissions);
    }

    public function isActionFullySelected(string $action): bool
    {
        $actionPermissions = [];

        foreach ($this->resources as $resource => $config) {
            if (in_array($action, $config['permissions'])) {
                $actionPermissions[] = "{$resource}.{$action}";
            }
        }

        return ! empty($actionPermissions) && count(array_intersect($actionPermissions, $this->selectedPermissions)) === count($actionPermissions);
    }

    public function getUniqueActions(): array
    {
        $actions = [];

        foreach ($this->resources as $config) {
            foreach ($config['permissions'] as $action) {
                $actions[$action] = ucfirst($action);
            }
        }

        return $actions;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($this->selectedPermissions)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least one permission.')
                ->danger()
                ->send();

            return;
        }

        // Update role
        $updateData = [
            'description' => $data['description'],
            'color' => $data['color'],
        ];

        if ( ! $this->role->is_system) {
            $updateData['name'] = $data['name'];
        }

        $this->role->update($updateData);

        // Sync permissions
        $this->role->syncPermissions($this->selectedPermissions);

        Notification::make()
            ->title('Role Updated')
            ->body('Role has been updated with ' . count($this->selectedPermissions) . ' permissions.')
            ->success()
            ->send();

        $this->redirect(route('management.roles'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('management.roles'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.management.edit-role');
    }
}
