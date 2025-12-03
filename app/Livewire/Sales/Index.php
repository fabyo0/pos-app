<?php

declare(strict_types=1);

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class Index extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Sale::query()->with(['customer', 'paymentMethod']))
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->description(fn($record): string => 'Sale #' . mb_str_pad((string) $record->id, 5, '0', STR_PAD_LEFT))
                    ->placeholder('Walk-in'),
                TextColumn::make('paymentMethod.name')
                    ->label('Payment')
                    ->badge()
                    ->icon(fn(?string $state): string => match ($state) {
                        'Cash' => 'heroicon-o-banknotes',
                        'Card' => 'heroicon-o-credit-card',
                        'Mobile Money' => 'heroicon-o-device-phone-mobile',
                        'Bank Transfer' => 'heroicon-o-building-library',
                        default => 'heroicon-o-currency-dollar',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'Cash' => 'success',
                        'Card' => 'info',
                        'Mobile Money' => 'warning',
                        'Bank Transfer' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->alignStart(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('payment_method_id')
                    ->label('Payment Method')
                    ->relationship('paymentMethod', 'name')
                    ->preload(),
                Filter::make('has_balance')
                    ->label('Unpaid Only')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->whereColumn('paid_amount', '<', 'total')),
                Filter::make('today')
                    ->label('Today')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->whereDate('created_at', today())),
            ])
            ->recordActions([
                ViewAction::make()->color('info'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No sales yet')
            ->emptyStateDescription('Create your first sale to get started.')
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public function render(): View
    {
        return view('livewire.sales.list-sales');
    }
}
