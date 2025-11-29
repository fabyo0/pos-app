<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Enums\RoleType;
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
use Illuminate\Support\Str;
use Livewire\Component;

final class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                  /*  ->copyable()
                    ->copyMessage('Copied!')
                    ->copyMessageDuration(1500)
                    ->tooltip('Click to copy')*/
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(RoleType $state) => Str::ucwords($state->value))
                    ->color(fn(RoleType $state): string => match ($state) {
                        RoleType::ADMIN => 'success',
                        RoleType::CASHIER => 'info',
                    })
                    ->icon(fn(RoleType $state): string => match ($state) {
                        RoleType::ADMIN => 'heroicon-o-shield-check',
                        RoleType::CASHIER => 'heroicon-o-user',
                    })
                    ->sortable(),
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
                SelectFilter::make('role')
                    ->options(RoleType::class)
                    ->label('Filter by Role'),
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
