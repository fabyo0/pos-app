<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Models\Customer;
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

final class Show extends Component implements HasSchemas
{
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
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name'),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->prefixIcon('heroicon-o-envelope'),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->prefixIcon('heroicon-o-phone'),
                        TextInput::make('company_name')
                            ->label('Company')
                            ->prefixIcon('heroicon-o-building-office'),
                        TextInput::make('tax_id')
                            ->label('Tax ID'),
                        Toggle::make('is_active')
                            ->label('Status')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle'),
                    ]),

                Section::make('Address Details')
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        TextInput::make('address')
                            ->label('Street Address')
                            ->columnSpanFull(),
                        TextInput::make('city')
                            ->label('City'),
                        TextInput::make('state')
                            ->label('State / Province'),
                        TextInput::make('postal_code')
                            ->label('Postal Code'),
                        TextInput::make('country')
                            ->label('Country'),
                    ]),

                Section::make('Notes')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(fn() => empty($this->record->notes))
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record)
            ->disabled();
    }

    public function delete(): void
    {
        $this->record->delete();

        Notification::make()
            ->title('Customer deleted')
            ->body('The customer has been successfully deleted.')
            ->success()
            ->send();

        $this->redirect(route('customers.index'));
    }

    public function render(): View
    {
        return view('livewire.customer.show');
    }
}
