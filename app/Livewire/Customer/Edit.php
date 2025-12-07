<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Edit extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Customer $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->description('Basic customer details and contact information.')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Doe')
                            ->autofocus(),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('john@example.com'),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->placeholder('+1 (555) 000-0000'),
                        TextInput::make('company_name')
                            ->label('Company')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-building-office')
                            ->placeholder('Acme Inc.'),
                        TextInput::make('tax_id')
                            ->label('Tax ID')
                            ->maxLength(50)
                            ->placeholder('XX-XXXXXXX'),
                        Toggle::make('is_active')
                            ->label('Active Customer')
                            ->default(true)
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('Address Details')
                    ->description('Customer billing and shipping address.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        TextInput::make('address')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('123 Main Street, Suite 100'),
                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(100)
                            ->placeholder('New York'),
                        TextInput::make('state')
                            ->label('State / Province')
                            ->maxLength(100)
                            ->placeholder('NY'),
                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->maxLength(20)
                            ->placeholder('10001'),
                        TextInput::make('country')
                            ->label('Country')
                            ->maxLength(100)
                            ->placeholder('United States'),
                    ]),

                Section::make('Additional Notes')
                    ->description('Internal notes about this customer.')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(fn(): bool => empty($this->record->notes))
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Add any relevant notes about this customer...')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Customer updated')
            ->body('Customer information has been saved successfully.')
            ->success()
            ->send();
    }

    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancel')
            ->color('gray')
            ->url(route('customers.show', $this->record));
    }

    public function render(): View
    {
        return view('livewire.customer.edit');
    }
}
