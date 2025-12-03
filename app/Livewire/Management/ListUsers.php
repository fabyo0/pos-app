<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('roles:name'),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('roles.name')
                    ->badge()
                    ->formatStateUsing(fn($state): string => ucfirst((string) $state))
                    ->color(fn($state): string => match ($state) {
                        'admin' => 'success',
                        'cashier' => 'info',
                    })
                    ->icon(fn($state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'cashier' => 'heroicon-o-user',
                        default => 'Unexpected match value',
                    }),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter by Role')
                    ->options([
                        'admin' => 'Admin',
                        'cashier' => 'Cashier',
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder
                        => $data['value']
                        ? $query->role($data['value'])
                         : $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.management.list-users');
    }
}
