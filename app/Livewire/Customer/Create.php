<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Create extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'is_active' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Customer name and company details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->minLength(2)
                                    ->maxLength(255)
                                    ->placeholder('John Doe')
                                    ->autofocus(),

                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->maxLength(255)
                                    ->placeholder('Acme Inc.'),
                            ]),

                        TextInput::make('tax_id')
                            ->label('Tax ID / VAT Number')
                            ->maxLength(50)
                            ->unique(Customer::class, 'tax_id')
                            ->placeholder('TR1234567890'),
                    ]),

                Section::make('Contact Information')
                    ->description('Email and phone details')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Customer::class, 'email')
                                    ->placeholder('john@example.com')
                                    ->prefixIcon('heroicon-o-envelope'),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->regex('/^[\+]?[(]?\d{3}[)]?[-\s\.]?\d{3}[-\s\.]?\d{4,6}$/')
                                    ->validationMessages([
                                        'regex' => 'Please enter a valid phone number.',
                                    ])
                                    ->placeholder('+90 555 123 4567')
                                    ->prefixIcon('heroicon-o-phone'),
                            ]),
                    ]),

                Section::make('Address')
                    ->description('Billing and shipping address')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->schema([
                        TextInput::make('address')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->placeholder('123 Main Street')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(100)
                                    ->alpha()
                                    ->placeholder('Istanbul'),

                                TextInput::make('state')
                                    ->label('State / Province')
                                    ->maxLength(100)

                                    ->placeholder('Marmara'),

                                TextInput::make('postal_code')
                                    ->label('Postal Code')
                                    ->maxLength(20)
                                    ->numeric()
                                    ->placeholder('34000'),
                            ]),

                        TextInput::make('country')
                            ->label('Country')
                            ->maxLength(100)
                            ->alpha()
                            ->placeholder('Turkey'),
                    ]),

                Section::make('Additional Information')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Any additional notes about this customer...')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active Customer')
                            ->helperText('Inactive customers won\'t appear in sales selection')
                            ->default(true),
                    ]),
            ])
            ->statePath('data')
            ->model(Customer::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Customer::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Customer created')
            ->body("Customer {$record->name} has been created successfully.")
            ->success()
            ->send();

        $this->redirect(route('customers.index'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.customer.create');
    }
}
