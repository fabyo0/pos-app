<?php

declare(strict_types=1);

namespace App\Livewire\Management;

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
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

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
                    ])->columnSpan(2)
                    ->columnSpanFull(),
                Section::make('Security')
                    ->description('Password and authentication settings')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->rule(Password::defaults())
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->revealable(),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required()
                            ->same('password')
                            ->revealable()
                            ->dehydrated(false),
                    ]),

                Section::make('Role & Permissions')
                    ->description('Assign roles to define user access levels')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record): string => Str::ucfirst($record->name))
                            ->placeholder('Select roles...')
                            ->helperText('Users can have multiple roles. Each role grants specific permissions.')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(),

            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $data['email_verified_at'] = Carbon::now();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('User created')
            ->body("User {$record->name} has been created successfully.")
            ->success()
            ->send();

        $this->redirect(route('management.users'), navigate: true);

    }

    public function render(): View
    {
        return view('livewire.management.create-user');
    }
}
