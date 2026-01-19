<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

final class AuthenticationLogs extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(AuthenticationLog::query()->with('authenticatable'))
            ->defaultSort('login_at', 'desc')
            ->columns([
                TextColumn::make('authenticatable.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (AuthenticationLog $record): string => $record->authenticatable?->email ?? 'Unknown'),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn (AuthenticationLog $record): string => $this->getEventLabel($record))
                    ->color(fn (AuthenticationLog $record): string => $this->getEventColor($record))
                    ->icon(fn (AuthenticationLog $record): string => $this->getEventIcon($record)),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-globe-alt'),

                TextColumn::make('user_agent')
                    ->label('Device')
                    ->formatStateUsing(fn (?string $state): string => $this->parseUserAgent($state))
                    ->description(fn (?string $state): string => $this->parseBrowser($state))
                    ->wrap()
                    ->lineClamp(2),

                TextColumn::make('login_at')
                    ->label('Time')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->description(fn (AuthenticationLog $record): string => $record->login_at?->diffForHumans() ?? ''),

                IconColumn::make('login_successful')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label('User')
                    ->relationship('authenticatable', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('event')
                    ->label('Event Type')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed' => 'Failed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'login' => $query->whereNotNull('login_at')->where('login_successful', true),
                            'logout' => $query->whereNotNull('logout_at'),
                            'failed' => $query->where('login_successful', false),
                            default => $query,
                        };
                    }),

                Filter::make('login_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From'),
                        DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '<=', $date),
                        ))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }

                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),

                Filter::make('failed_only')
                    ->label('Failed Attempts Only')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('login_successful', false)),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Authentication Log Details')
                    ->modalContent(fn (AuthenticationLog $record): View => view('livewire.management.partials.auth-log-details', ['log' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                Action::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->deselectRecordsAfterCompletion(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn () => $this->export()),

                Action::make('clear_old')
                    ->label('Clear Old Logs')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This will delete all authentication logs older than 30 days.')
                    ->action(fn () => $this->clearOldLogs()),
            ])
            ->emptyStateHeading('No authentication logs')
            ->emptyStateDescription('Authentication events will appear here once users start logging in.')
            ->emptyStateIcon('heroicon-o-finger-print')
            ->poll('30s');
    }

    public function export(): void
    {
        Notification::make()
            ->title('Export Started')
            ->body('Your export is being prepared...')
            ->info()
            ->send();

        // TODO: Implement Excel export
    }

    public function clearOldLogs(): void
    {
        $deleted = AuthenticationLog::where('login_at', '<', now()->subDays(30))->delete();

        Notification::make()
            ->title('Logs Cleared')
            ->body("{$deleted} old logs have been deleted.")
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.management.authentication-logs');
    }

    private function getEventLabel(AuthenticationLog $record): string
    {
        if ($record->logout_at) {
            return 'Logout';
        }

        return $record->login_successful ? 'Login' : 'Failed';
    }

    private function getEventColor(AuthenticationLog $record): string
    {
        if ($record->logout_at) {
            return 'gray';
        }

        return $record->login_successful ? 'success' : 'danger';
    }

    private function getEventIcon(AuthenticationLog $record): string
    {
        if ($record->logout_at) {
            return 'heroicon-o-arrow-right-start-on-rectangle';
        }

        return $record->login_successful
            ? 'heroicon-o-arrow-left-end-on-rectangle'
            : 'heroicon-o-x-circle';
    }

    private function parseUserAgent(?string $userAgent): string
    {
        if ( ! $userAgent) {
            return 'Unknown Device';
        }

        // Simple OS detection
        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            str_contains($userAgent, 'iPhone') => 'iPhone',
            str_contains($userAgent, 'iPad') => 'iPad',
            str_contains($userAgent, 'Android') => 'Android',
            default => 'Unknown',
        };
    }

    private function parseBrowser(?string $userAgent): string
    {
        if ( ! $userAgent) {
            return 'Unknown Browser';
        }

        return match (true) {
            str_contains($userAgent, 'Chrome') && ! str_contains($userAgent, 'Edg') => 'Chrome',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Safari') && ! str_contains($userAgent, 'Chrome') => 'Safari',
            str_contains($userAgent, 'Edg') => 'Edge',
            str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR') => 'Opera',
            default => 'Unknown',
        };
    }
}
