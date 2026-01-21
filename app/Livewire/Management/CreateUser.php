<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Enums\RoleType;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

final class CreateUser extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->description('Basic user account details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('John Doe')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('john@example.com')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email'),
                    ])
                    ->columns(2),

                Section::make('Security')
                    ->description('Password and authentication settings')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->rule(Password::defaults())
                            ->revealable(),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required()
                            ->same('password')
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Role & Permissions')
                    ->description('Assign roles to define user access levels')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->options(
                                Role::query()
                                    ->pluck('name', 'name')
                                    ->mapWithKeys(fn($name) => [
                                        $name => RoleType::getLabel($name),
                                    ])
                                    ->toArray(),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Select roles...')
                            ->helperText('Users can have multiple roles. Each role grants specific permissions.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = Carbon::now();

        $user = User::create($data);

        if ( ! empty($roles)) {
            $user->syncRoles($roles);
        }

        Notification::make()
            ->title('User created')
            ->body("User {$user->name} has been created successfully.")
            ->success()
            ->send();

        $this->redirect(route('management.users'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.management.create-user');
    }
}
