<?php

declare(strict_types=1);

namespace App\Livewire\Inventory;

use App\Enums\ItemStatus;
use App\Models\Inventory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;

final class Index extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Inventory::query()->with('item'))
            ->columns([
                TextColumn::make('item.name')
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-cube')
                    ->description(fn($record): string => "SKU: {$record->item->sku}")
                    ->formatStateUsing(fn($state): string => ucfirst((string) $state))
                    ->searchable(),

                TextColumn::make('item.price')
                    ->label('Unit Price')
                    ->money()
                    ->color('info')
                    ->sortable()
                    ->alignLeft(),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->alignCenter(),

                TextColumn::make('stock_value')
                    ->label('Stock Value')
                    ->getStateUsing(fn($record): float => $record->quantity * $record->item->price)
                    ->money('USD')
                    ->color('success')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('item.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(ItemStatus $state): string => ucfirst($state->getLabel()))
                    ->color(fn(ItemStatus $state): string => match ($state) {
                        ItemStatus::ACTIVE => 'success',
                        ItemStatus::INACTIVE => 'danger',
                    })
                    ->icon(fn(ItemStatus $state): string => match ($state) {
                        ItemStatus::ACTIVE => 'heroicon-o-check-circle',
                        ItemStatus::INACTIVE => 'heroicon-o-x-circle',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Item Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder => $data['value']
                            ? $query->whereHas('item', fn($q) => $q->where('status', $data['value']))
                            : $query,
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Item')
                    ->model(Inventory::class)
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Add New Inventory')
                    ->modalDescription('Add stock for a product that doesn\'t have inventory yet.')
                    ->modalIcon('heroicon-o-archive-box')
                    ->schema([
                        Select::make('item_id')
                            ->label('Product')
                            ->formatStateUsing(fn($state): string => Str::ucfirst($state))
                            ->preload()
                            ->relationship('item', 'name')
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-cube')
                            ->placeholder('Select a product')
                            ->noSearchResultsMessage('No products without inventory found'),

                        TextInput::make('quantity')
                            ->label('Initial Quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->prefixIcon('heroicon-o-hashtag')
                            ->hint('Enter the initial stock quantity'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Inventory Details')
                    ->modalIcon('heroicon-o-archive-box')
                    ->schema([
                        Section::make('Product Information')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                TextEntry::make('item.name')
                                    ->label('Product')
                                    ->size('sm')
                                    ->weight('bold')
                                    ->icon('heroicon-o-cube'),
                                TextEntry::make('item.sku')
                                    ->label('SKU')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-qr-code'),
                                TextEntry::make('item.price')
                                    ->label('Unit Price')
                                    ->money('USD')
                                    ->color('primary')
                                    ->icon('heroicon-o-currency-dollar'),
                                TextEntry::make('item.status')
                                    ->label('Status')
                                    ->badge(),
                            ])
                            ->collapsible()
                            ->columns(2),
                        Section::make('Stock Details')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                TextEntry::make('quantity')
                                    ->label('Current Stock')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->badge()
                                    ->icon('heroicon-o-archive-box')
                                    ->color(fn($state): string => match (true) {
                                        $state <= 0 => 'danger',
                                        $state <= 10 => 'warning',
                                        default => 'success',
                                    }),
                                TextEntry::make('stock_value')
                                    ->label('Total Value')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->getStateUsing(fn($record): float => $record->quantity * $record->item->price)
                                    ->money('USD')
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),
                                TextEntry::make('created_at')
                                    ->label('Added')
                                    ->dateTime('M d, Y')
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M d, Y H:i')
                                    ->icon('heroicon-o-clock'),
                            ])
                            ->collapsible()
                            ->columns(2),
                    ]),
                EditAction::make()
                    ->modalHeading('Update Stock')
                    ->modalDescription('Adjust the inventory quantity for this product.')
                    ->modalIcon('heroicon-o-archive-box')
                    ->schema([
                        Select::make('item_id')
                            ->label('Product')
                            ->relationship('item', 'name')
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->prefixIcon('heroicon-o-cube'),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefixIcon('heroicon-o-hashtag')
                            ->hint('Enter the current stock level'),
                    ]),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                ]),
            ])
            ->emptyStateHeading('No inventory records')
            ->emptyStateDescription('Inventory will appear here when items are added.')
            ->emptyStateIcon('heroicon-o-archive-box')
            ->defaultSort('quantity', 'asc')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.items.list-inventories');
    }
}
