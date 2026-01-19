<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Permission\Models\Role;

final class Roles extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public string $search = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(Role::query()->orderBy('sort_order'))
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->badge()
                    ->color(fn (Role $record): string => $record->color ?? 'gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('blue')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn (Role $record): string => route('management.roles.edit', $record)),

                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Role')
                    ->modalDescription(
                        fn (Role $record): string => $record->is_system
                            ? 'This is a system role and cannot be deleted.'
                            : 'Are you sure you want to delete this role? Users with this role will lose their permissions.',
                    )
                    ->modalSubmitActionLabel('Delete Role')
                    ->disabled(fn (Role $record): int|bool => $record->is_system)
                    ->action(fn (Role $record) => $this->deleteRole($record)),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Roles')
                    ->modalDescription('Are you sure you want to delete the selected roles?')
                    ->action(fn (Collection $records) => $this->deleteMultiple($records)),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Create Role')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(route('management.roles.create')),
            ])
            ->emptyStateHeading('No roles found')
            ->emptyStateDescription('Create your first role to get started.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }

    public function deleteRole(Role $role): void
    {
        if ($role->is_system) {
            Notification::make()
                ->title('Cannot Delete System Role')
                ->body('System roles cannot be deleted.')
                ->danger()
                ->send();

            return;
        }

        $roleName = $role->name;
        $role->delete();

        Notification::make()
            ->title('Role Deleted')
            ->body("Role '{$roleName}' has been deleted successfully.")
            ->success()
            ->send();
    }

    public function deleteMultiple(Collection $records): void
    {
        $deleted = 0;
        $skipped = 0;

        foreach ($records as $record) {
            if ($record->is_system) {
                $skipped++;

                continue;
            }

            $record->delete();
            $deleted++;
        }

        if ($deleted > 0) {
            Notification::make()
                ->title('Roles Deleted')
                ->body("{$deleted} role(s) deleted successfully." . ($skipped > 0 ? " {$skipped} system role(s) were skipped." : ''))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('No Roles Deleted')
                ->body('Only system roles were selected. System roles cannot be deleted.')
                ->warning()
                ->send();
        }
    }

    public function getRolesProperty(): Collection
    {
        return Role::query()
            ->withCount(['users', 'permissions'])
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading('Delete Role')
            ->modalDescription(
                fn (array $arguments) => 'Are you sure you want to delete this role? Users with this role will lose their permissions.',
            )
            ->modalSubmitActionLabel('Delete Role')
            ->color('danger')
            ->action(function (array $arguments): void {
                $role = Role::find($arguments['role']);

                if ( ! $role) {
                    return;
                }

                if ($role->is_system) {
                    Notification::make()
                        ->title('Cannot Delete')
                        ->body('System roles cannot be deleted.')
                        ->danger()
                        ->send();

                    return;
                }

                $roleName = $role->name;
                $role->delete();

                Notification::make()
                    ->title('Role Deleted')
                    ->body("Role '{$roleName}' has been deleted successfully.")
                    ->success()
                    ->send();
            });
    }

    public function getRoleColor(string $color = 'gray', float $opacity = 1): string
    {
        $colors = [
            'gray' => 'rgb(107, 114, 128)',
            'red' => 'rgb(239, 68, 68)',
            'orange' => 'rgb(249, 115, 22)',
            'yellow' => 'rgb(234, 179, 8)',
            'green' => 'rgb(34, 197, 94)',
            'blue' => 'rgb(59, 130, 246)',
            'indigo' => 'rgb(99, 102, 241)',
            'purple' => 'rgb(168, 85, 247)',
            'pink' => 'rgb(236, 72, 153)',
        ];

        $rgb = $colors[$color] ?? $colors['gray'];

        if ($opacity < 1) {
            return str_replace('rgb', 'rgba', str_replace(')', ", {$opacity})", $rgb));
        }

        return $rgb;
    }

    public function render(): Factory|View
    {
        return view('livewire.management.roles');
    }
}
