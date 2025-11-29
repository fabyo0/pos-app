<?php

declare(strict_types=1);

namespace App\Livewire\Items;

use App\Enums\ItemStatus;
use App\Models\Inventory;
use Filament\Actions\BulkActionGroup;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class ListInventories extends Component implements HasActions, HasSchemas, HasTable
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

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
