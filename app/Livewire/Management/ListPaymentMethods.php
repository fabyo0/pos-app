<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Models\PaymentMethod;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
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
                ViewAction::make()
                    ->modalHeading(fn(PaymentMethod $record): string => $record->name)
                    ->modalDescription('Payment Method Details')
                    ->modalIcon(fn(PaymentMethod $record): string => match ($record->name) {
                        'Cash' => 'heroicon-o-banknotes',
                        'Card' => 'heroicon-o-credit-card',
                        'Mobile Money' => 'heroicon-o-device-phone-mobile',
                        'Bank Transfer' => 'heroicon-o-building-library',
                        default => 'heroicon-o-currency-dollar',
                    })
                    ->modalWidth('2xl')
                    ->schema([
                        // Stats Cards
                        Grid::make(3)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('sales_count')
                                            ->label('Total Transactions')
                                            ->state(fn(PaymentMethod $record): int => $record->sales()->count())
                                            ->icon('heroicon-o-shopping-cart')
                                            ->color('info')
                                            ->weight(FontWeight::Bold)
                                            ->size('lg'),
                                    ]),

                                Section::make()
                                    ->schema([
                                        TextEntry::make('total_revenue')
                                            ->label('Total Revenue')
                                            ->state(fn(PaymentMethod $record): string|float => $record->sales()->sum('total'))
                                            ->money()
                                            ->icon('heroicon-o-currency-dollar')
                                            ->color('success')
                                            ->weight(FontWeight::Bold)
                                            ->size('lg'),
                                    ]),

                                Section::make()
                                    ->schema([
                                        TextEntry::make('is_active')
                                            ->label('Status')
                                            ->badge()
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                            ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                                    ]),
                            ]),

                        Section::make('Payment Method Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Name')
                                            ->weight(FontWeight::Bold)
                                            ->icon(fn(PaymentMethod $record): string => match ($record->name) {
                                                'Cash' => 'heroicon-o-banknotes',
                                                'Card' => 'heroicon-o-credit-card',
                                                'Mobile Money' => 'heroicon-o-device-phone-mobile',
                                                'Bank Transfer' => 'heroicon-o-building-library',
                                                default => 'heroicon-o-currency-dollar',
                                            }),

                                        TextEntry::make('type_description')
                                            ->label('Type')
                                            ->state(fn(PaymentMethod $record): string => match ($record->name) {
                                                'Cash' => 'Physical currency payment',
                                                'Card' => 'Credit or debit card',
                                                'Mobile Money' => 'Digital wallet payment',
                                                'Bank Transfer' => 'Direct bank transfer',
                                                default => 'Other payment method',
                                            })
                                            ->color('gray'),
                                    ]),
                            ]),

                        Section::make('Statistics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('avg_transaction')
                                            ->label('Average Transaction')
                                            ->state(fn(PaymentMethod $record): float|string => $record->sales()->avg('total') ?? 0)
                                            ->money()
                                            ->icon('heroicon-o-calculator')
                                            ->color('warning'),

                                        TextEntry::make('last_used')
                                            ->label('Last Used')
                                            ->state(fn(PaymentMethod $record): string => $record->sales()->latest()->first()?->created_at?->diffForHumans() ?? 'Never')
                                            ->icon('heroicon-o-clock')
                                            ->color('gray'),
                                    ]),
                            ]),

                        Section::make('Timeline')
                            ->icon('heroicon-o-clock')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Created')
                                            ->dateTime('M d, Y - H:i')
                                            ->icon('heroicon-o-calendar'),

                                        TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->since()
                                            ->icon('heroicon-o-arrow-path'),
                                    ]),
                            ]),
                    ]),
                EditAction::make()
                    ->modalHeading(fn(PaymentMethod $record): string => "Edit {$record->name}")
                    ->modalDescription('Update payment method details')
                    ->modalIcon('heroicon-o-pencil-square')
                    ->modalWidth('2xl')
                    ->schema([
                        TextInput::make('name')
                            ->label('Payment Method Name')
                            ->required()
                            ->maxLength(20)
                            ->unique(table: PaymentMethod::class, column: 'name', ignoreRecord: true)
                            ->placeholder('e.g. Cash, Card, Bank Transfer')
                            ->prefixIcon('heroicon-o-credit-card'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Inactive payment methods won\'t appear in sales')
                            ->default(true),
                    ])
                ,
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
