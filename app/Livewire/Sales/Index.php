<?php

declare(strict_types=1);

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
            ->query(Sale::query()->with(['customer', 'paymentMethod', 'salesItems.item']))
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
                ViewAction::make()
                    ->modalHeading(fn(Sale $record): string => 'Sale #' . mb_str_pad((string) $record->id, 5, '0', STR_PAD_LEFT))
                    ->modalDescription(fn(Sale $record): string => $record->created_at->format('M d, Y - H:i'))
                    ->modalIcon('heroicon-o-shopping-cart')
                    ->modalWidth('4xl')
                    ->color('info')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('total')
                                            ->label('Total')
                                            ->money('USD')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->color('success')
                                            ->weight('bold')
                                            ->size('lg'),
                                    ]),

                                Section::make()
                                    ->schema([
                                        TextEntry::make('paid_amount')
                                            ->label('Paid')
                                            ->money('USD')
                                            ->icon('heroicon-o-banknotes')
                                            ->color('info')
                                            ->weight('bold')
                                            ->size('lg'),
                                    ]),

                                Section::make()
                                    ->schema([
                                        TextEntry::make('balance')
                                            ->label('Balance')
                                            ->state(fn(Sale $record): float => (float) $record->total - (float) $record->paid_amount)
                                            ->money('USD')
                                            ->icon('heroicon-o-calculator')
                                            ->color(fn(Sale $record): string => ((float) $record->total - (float) $record->paid_amount) > 0 ? 'danger' : 'success')
                                            ->weight('bold')
                                            ->size('lg'),
                                    ]),

                                Section::make()
                                    ->schema([
                                        TextEntry::make('discount')
                                            ->label('Discount')
                                            ->money('USD')
                                            ->icon('heroicon-o-tag')
                                            ->color('warning')
                                            ->weight('bold')
                                            ->size('lg'),
                                    ]),
                            ]),

                        // Customer & Payment Info
                        Grid::make(2)
                            ->schema([
                                Section::make('Customer')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('customer.name')
                                            ->label('Name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('Walk-in Customer'),

                                        TextEntry::make('customer.email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->placeholder('No email'),

                                        TextEntry::make('customer.phone')
                                            ->label('Phone')
                                            ->icon('heroicon-o-phone')
                                            ->copyable()
                                            ->placeholder('No phone'),
                                    ]),

                                Section::make('Payment')
                                    ->icon('heroicon-o-credit-card')
                                    ->schema([
                                        TextEntry::make('paymentMethod.name')
                                            ->label('Method')
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

                                        TextEntry::make('payment_status')
                                            ->label('Status')
                                            ->badge()
                                            ->state(fn(Sale $record): string => ((float) $record->total - (float) $record->paid_amount) <= 0 ? 'Paid' : 'Pending')
                                            ->icon(fn(Sale $record): string => ((float) $record->total - (float) $record->paid_amount) <= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                                            ->color(fn(Sale $record): string => ((float) $record->total - (float) $record->paid_amount) <= 0 ? 'success' : 'warning'),

                                        TextEntry::make('created_at')
                                            ->label('Date')
                                            ->dateTime('M d, Y - H:i')
                                            ->icon('heroicon-o-calendar'),
                                    ]),
                            ]),

                        // Items
                        Section::make('Items')
                            ->icon('heroicon-o-shopping-bag')
                            ->description(fn(Sale $record): string => $record->salesItems->count() . ' items')
                            ->schema([
                                RepeatableEntry::make('salesItems')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('item.name')
                                            ->label('Product')
                                            ->weight('bold'),

                                        TextEntry::make('quantity')
                                            ->label('Qty')
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('price')
                                            ->label('Unit Price')
                                            ->money('USD'),

                                        TextEntry::make('subtotal')
                                            ->label('Subtotal')
                                            ->state(fn($record): float => (float) $record->quantity * (float) $record->price)
                                            ->money('USD')
                                            ->color('success')
                                            ->weight('bold'),
                                    ])
                                    ->columns(4),
                            ])
                            ->collapsed()
                            ->extraAttributes(['class' => 'max-h-64 overflow-y-auto']),
                    ]),
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
