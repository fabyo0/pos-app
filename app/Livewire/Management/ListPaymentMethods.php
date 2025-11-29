<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Models\PaymentMethod;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ListPaymentMethods extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PaymentMethod::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Payment Method')
                    ->sortable()
                    ->weight('bold')
                    ->icon(fn(string $state): string => match ($state) {
                        'Cash' => 'heroicon-o-banknotes',
                        'Card' => 'heroicon-o-credit-card',
                        'Mobile Money' => 'heroicon-o-device-phone-mobile',
                        'Bank Transfer' => 'heroicon-o-building-library',
                        default => 'heroicon-o-currency-dollar',
                    })
                    ->description(fn(string $state): string => match ($state) {
                        'Cash' => 'Physical currency payment',
                        'Card' => 'Credit or debit card',
                        'Mobile Money' => 'Digital wallet payment',
                        'Bank Transfer' => 'Direct bank transfer',
                        default => '',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter(),
                TextColumn::make('sales_count')
                    ->label('Total Sales')
                    ->counts('sales')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sales_sum_total')
                    ->label('Revenue')
                    ->sum('sales', 'total')
                    ->money()
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
            ])
            ->emptyStateHeading('No payment methods')
            ->emptyStateDescription('Create your first payment method to get started.')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->defaultSort('name', 'asc')
            ->striped()
            ->defaultSort('name', 'asc')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.management.list-payment-methods');
    }
}
