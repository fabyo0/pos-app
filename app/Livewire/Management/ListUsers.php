<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

final class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('roles:name'),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('roles.name')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'manager' => 'Manager',
                        'cashier' => 'Cashier',
                        'warehouse' => 'Warehouse',
                        'accountant' => 'Accountant',
                        'viewer' => 'Viewer',
                        default => ucfirst(str_replace('_', ' ', (string) $state)),
                    })
                    ->color(fn($state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'info',
                        'manager' => 'success',
                        'cashier' => 'warning',
                        'warehouse' => 'orange',
                        'accountant' => 'indigo',
                        'viewer' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn($state): string => match ($state) {
                        'super_admin' => 'heroicon-o-shield-exclamation',
                        'admin' => 'heroicon-o-shield-check',
                        'manager' => 'heroicon-o-briefcase',
                        'cashier' => 'heroicon-o-banknotes',
                        'warehouse' => 'heroicon-o-cube',
                        'accountant' => 'heroicon-o-calculator',
                        'viewer' => 'heroicon-o-eye',
                        default => 'heroicon-o-user',
                    }),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter by Role')
                    ->options([
                        'admin' => 'Admin',
                        'cashier' => 'Cashier',
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder => $data['value']
                            ? $query->role($data['value'])
                            : $query,
                    ),
            ])
            ->headerActions([
                Action::make('#')
                    ->label('Create User')
                    ->icon(Heroicon::Plus)
                    ->url(fn(): string => route('management.user.create')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn(User $record) => $record->name)
                    ->modalDescription('User account details and activity')
                    ->modalIcon('heroicon-o-user-circle')
                    ->modalWidth('3xl')
                    ->schema([
                        Flex::make([
                            TextEntry::make('email')
                                ->hiddenLabel()
                                ->icon('heroicon-m-envelope')
                                ->iconColor('gray')
                                ->color('gray'),
                            TextEntry::make('email_verified_at')
                                ->hiddenLabel()
                                ->badge()
                                ->state(
                                    fn(User $record): string => $record->email_verified_at ? 'Verified' : 'Unverified',
                                )
                                ->color(
                                    fn(User $record): string => $record->email_verified_at ? 'success' : 'warning',
                                )
                                ->icon(
                                    fn(User $record): string => $record->email_verified_at ? 'heroicon-m-check-badge' : 'heroicon-m-clock',
                                )
                                ->grow(false),
                        ])->from('sm'),

                        Section::make('Roles & Permissions')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                TextEntry::make('roles.name')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->color('primary')
                                    ->separator(',')
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                                    ->placeholder('No roles assigned'),
                            ])
                            ->columnSpanFull(),

                        Section::make('Account Activity')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Member Since')
                                    ->dateTime('F j, Y')
                                    ->icon('heroicon-m-calendar')
                                    ->iconColor('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('F j, Y \a\t H:i')
                                    ->icon('heroicon-m-arrow-path')
                                    ->iconColor('gray'),

                                TextEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->dateTime('F j, Y \a\t H:i')
                                    ->icon('heroicon-m-check-circle')
                                    ->iconColor('success')
                                    ->placeholder('Not verified yet'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),

                        Section::make('Security')
                            ->icon('heroicon-m-lock-closed')
                            ->schema([
                                TextEntry::make('two_factor_confirmed_at')
                                    ->label('Two-Factor Auth')
                                    ->badge()
                                    ->state(
                                        fn(User $record): string => $record->two_factor_confirmed_at ? 'Enabled' : 'Disabled',
                                    )
                                    ->color(
                                        fn(User $record): string => $record->two_factor_confirmed_at ? 'success' : 'gray',
                                    )
                                    ->icon(
                                        fn(User $record): string => $record->two_factor_confirmed_at ? 'heroicon-m-shield-check' : 'heroicon-m-shield-exclamation',
                                    ),

                                TextEntry::make('deleted_at')
                                    ->label('Account Status')
                                    ->badge()
                                    ->state(
                                        fn(User $record): string => $record->deleted_at ? 'Suspended' : 'Active',
                                    )
                                    ->color(
                                        fn(User $record): string => $record->deleted_at ? 'danger' : 'success',
                                    )
                                    ->icon(
                                        fn(User $record): string => $record->deleted_at ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle',
                                    ),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
                EditAction::make()
                    ->modalHeading(fn(User $record) => $record->name)
                    ->modalDescription('Edit user account details and permissions')
                    ->modalIcon('heroicon-o-user-circle')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('User Information')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->placeholder('john@example.com')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ])
                            ->columnSpan(2)
                            ->columnSpanFull(),

                        Section::make('Security')
                            ->description('Leave password fields empty to keep current password')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                TextInput::make('password')
                                    ->label('New Password')
                                    ->placeholder('••••••••')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(
                                        fn(?string $state): ?string => filled($state) ? Hash::make($state) : null,
                                    )
                                    ->dehydrated(fn(?string $state): bool => filled($state))
                                    ->rule(Password::defaults())
                                    ->columnSpan(1),

                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->placeholder('••••••••')
                                    ->password()
                                    ->revealable()
                                    ->same('password')
                                    ->requiredWith('password')
                                    ->dehydrated(false)
                                    ->columnSpan(1),
                            ])
                            ->columns()
                            ->collapsible()
                            ->columnSpanFull(),

                        Section::make('Role & Permissions')
                            ->description('Manage user access levels')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Select::make('roles')
                                    ->label('Assigned Roles')
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn($record): string => ucfirst((string) $record->name))
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Select roles...')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->mutateRecordDataUsing(function (array $data): array {
                        unset($data['password']);

                        return $data;
                    })
                    ->successNotificationTitle('User updated successfully'),

                DeleteAction::make()
                    ->modalHeading('Delete User')
                    ->modalDescription(fn(User $record): string => "Are you sure you want to delete {$record->name}? This action cannot be undone.")
                    ->modalIcon('heroicon-o-trash')
                    ->successNotificationTitle('User deleted successfully'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.management.list-users');
    }
}
