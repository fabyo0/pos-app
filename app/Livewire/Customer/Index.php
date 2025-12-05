<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Index extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Customer::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record): ?string => $record->company_name),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('No email'),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('No phone'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter(),
                TextColumn::make('sales_count')
                    ->label('Orders')
                    ->counts('sales')
                    ->badge()
                    ->sortable()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn(Customer $record): string => route('customers.show', $record)),
                EditAction::make()
                    ->url(fn(Customer $record): string => route('customers.edit', $record)),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('Create')
                    ->label('Create Item')
                    ->icon(Heroicon::Plus)
                    ->url(fn(): string => route('customers.create')),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
            ])
            ->emptyStateHeading('No customers yet')
            ->emptyStateDescription('Create your first customer to get started.')
            ->emptyStateIcon('heroicon-o-users')
            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public function render(): View
    {
        return view('livewire.customer.customers');
    }
}
