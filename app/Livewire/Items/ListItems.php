<?php

declare(strict_types=1);

namespace App\Livewire\Items;

use App\Enums\ItemStatus;
use App\Models\Item;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class ListItems extends Component implements HasActions, HasSchemas, HasTable
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
                    ->description(fn($record): string => "SKU : {$record->sku}")
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money()
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd(),

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

            ])
            ->recordActions([
                ViewAction::make()->color('info'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No items yet')
            ->emptyStateDescription('Create your first product to get started.')
            ->emptyStateIcon('heroicon-o-cube')
            ->defaultSort('name', 'asc')
            ->striped()
            ->toolbarActions([
            ]);
    }

    public function render(): View
    {
        return view('livewire.items.list-items');
    }
}
