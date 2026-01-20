<?php

declare(strict_types=1);

namespace App\Livewire\Management;

use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Stevebauman\Location\Facades\Location;

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
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph(
                            'authenticatable',
                            [User::class],
                            function (Builder $q) use ($search): void {
                                $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            },
                        );
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy(
                        User::select('name')
                            ->whereColumn('users.id', 'authentication_log.authenticatable_id')
                            ->where('authentication_log.authenticatable_type', User::class)
                            ->limit(1),
                        $direction,
                    ))
                    ->description(fn (AuthenticationLog $record): string => $record->authenticatable?->email ?? ''),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->getStateUsing(fn (AuthenticationLog $record): string => $this->getEventLabel($record))
                    ->color(fn (AuthenticationLog $record): string => $this->getEventColor($record))
                    ->icon(fn (AuthenticationLog $record): string => $this->getEventIcon($record)),

                // Location with Flag
                ViewColumn::make('location')
                    ->label('Location')
                    ->view('partials.location-column'),

                TextColumn::make('user_agent')
                    ->label('Device')
                    ->getStateUsing(fn (AuthenticationLog $record): string => $this->parseUserAgent($record->user_agent))
                    ->description(fn (AuthenticationLog $record): string => $this->parseBrowser($record->user_agent))
                    ->icon(fn (AuthenticationLog $record): string => $this->getDeviceIcon($record->user_agent)),

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
                SelectFilter::make('authenticatable_id')
                    ->label('User')
                    ->options(
                        User::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray(),
                    )
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if ( ! empty($data['value'])) {
                            return $query->where('authenticatable_id', $data['value'])
                                ->where('authenticatable_type', User::class);
                        }

                        return $query;
                    }),

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
                            $data['from'] ?? null,
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                        )
                        ->when(
                            $data['until'] ?? null,
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
            ->actions([
                Action::make('view')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Authentication Log Details')
                    ->modalContent(fn (AuthenticationLog $record): View => view(
                        'partials.auth-log-details',
                        [
                            'log' => $record,
                            'location' => $this->getLocationFromIp($record->ip_address),
                        ],
                    ))
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

    /**
     * Get location data from IP address with caching
     */
    public function getLocationFromIp(?string $ip): ?object
    {
        if ( ! $ip || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return (object) [
                'countryCode' => 'LOCAL',
                'countryName' => 'Local Network',
                'cityName' => 'Localhost',
                'ip' => $ip,
            ];
        }

        return Cache::remember("ip_location_{$ip}", now()->addDay(), function () use ($ip) {
            try {
                return Location::get($ip);
            } catch (Exception $e) {
                return null;
            }
        });
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

    private function getDeviceIcon(?string $userAgent): string
    {
        if ( ! $userAgent) {
            return 'heroicon-o-device-phone-mobile';
        }

        return match (true) {
            str_contains($userAgent, 'iPhone'),
            str_contains($userAgent, 'Android') && str_contains($userAgent, 'Mobile') => 'heroicon-o-device-phone-mobile',
            str_contains($userAgent, 'iPad'),
            str_contains($userAgent, 'Android') && ! str_contains($userAgent, 'Mobile') => 'heroicon-o-device-tablet',
            default => 'heroicon-o-computer-desktop',
        };
    }
}
