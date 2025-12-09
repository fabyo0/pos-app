<?php

declare(strict_types=1);

namespace App\Livewire\Items;

use App\Enums\ItemStatus;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
            ->query(fn(): Builder => Item::query())
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->icon('heroicon-o-cube')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('inventory.quantity')
                    ->label('Stock')
                    ->badge()
                    ->default(0)
                    ->sortable()
                    ->color(fn($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->alignCenter(),

                TextColumn::make('sales_items_count')
                    ->label('Sold')
                    ->counts('salesItems')
                    ->badge()
                    ->sortable()
                    ->color('info')
                    ->alignCenter(),

                ToggleColumn::make('status')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter()
                    ->updateStateUsing(function ($record, $state): void {
                        $record->status = $state ? ItemStatus::ACTIVE : ItemStatus::INACTIVE;
                        $record->save();
                    })
                    ->getStateUsing(fn($record): bool => $record->status === ItemStatus::ACTIVE),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ItemStatus::class),

                Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn(Builder $query): Builder => $query->whereHas('inventory', fn($q) => $q->where('quantity', '<=', 10))),
            ])
            ->headerActions([
                Action::make('Create')
                    ->label('Create Item')
                    ->url(fn(): string => route('items.create')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => $record->name)
                    ->modalIcon('heroicon-o-cube')
                    ->modalWidth('lg')
                    ->infolist([
                        Flex::make([
                            TextEntry::make('sku')
                                ->hiddenLabel()
                                ->badge()
                                ->color('gray')
                                ->icon('heroicon-m-qr-code'),
                            TextEntry::make('status')
                                ->hiddenLabel()
                                ->badge()
                                ->grow(false),
                        ])->from('sm'),

                        Section::make('Metrics')
                            ->icon('heroicon-m-chart-bar')
                            ->schema([
                                TextEntry::make('price')
                                    ->label('Unit Price')
                                    ->money()
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success')
                                    ->icon('heroicon-m-currency-dollar'),

                                TextEntry::make('inventory.quantity')
                                    ->label('In Stock')
                                    ->default(0)
                                    ->size('lg')
                                    ->weight('bold')
                                    ->badge()
                                    ->color(fn($state): string => match (true) {
                                        $state <= 0 => 'danger',
                                        $state <= 10 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-m-archive-box'),

                                TextEntry::make('sales_items_count')
                                    ->label('Total Sold')
                                    ->state(fn($record) => $record->salesItems()->count())
                                    ->size('lg')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-m-shopping-cart'),

                                TextEntry::make('stock_value')
                                    ->label('Stock Value')
                                    ->state(fn($record): int|float => ($record->inventory ?? 0) * $record->price)
                                    ->money()
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning')
                                    ->icon('heroicon-m-banknotes'),
                            ])
                            ->columns(2)
                            ->compact()
                            ->columnSpanFull(),

                        Fieldset::make('Timeline')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('M d, Y')
                                    ->icon('heroicon-m-calendar')
                                    ->iconColor('gray'),
                                TextEntry::make('updated_at')
                                    ->label('Last Update')
                                    ->dateTime('M d, Y H:i')
                                    ->icon('heroicon-m-arrow-path')
                                    ->iconColor('gray'),
                            ])
                            ->columns()
                            ->columnSpanFull(),
                    ]),

                EditAction::make()
                    ->url(fn(Item $record): string => route('items.edit', $record)),

                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No items yet')
            ->emptyStateDescription('Create your first product to get started.')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.items.list-items');
    }
}
