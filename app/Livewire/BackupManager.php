<?php

declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Log;

final class BackupManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => null)
            ->columns([
                TextColumn::make('name')
                    ->label('Backup Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('size')
                    ->label('Size')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(array $record) => $this->downloadBackup($record['path'])),

                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(array $record) => $this->deleteBackup($record['path'])),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $this->deleteMultiple($records)),

                BulkAction::make('download_all')
                    ->label('Download Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(Collection $records) => $this->downloadMultiple($records)),

            ])

            ->headerActions([
                Action::make('create')
                    ->label('Create Backup')
                    ->icon('heroicon-o-circle-stack')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Create Database Backup')
                    ->modalDescription('This will create a backup of your database. Are you sure?')
                    ->action(fn() => $this->createBackup()),
            ])
            ->paginated(false)
            ->records(fn() => $this->getBackups())
            ->emptyStateHeading('No backups found')
            ->emptyStateDescription('Create your first backup by clicking the button above.')
            ->emptyStateIcon('heroicon-o-circle-stack');
    }

    public function createBackup(): void
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();

            Notification::make()
                ->title('Backup Created Successfully')
                ->body('Your database backup has been created.')
                ->success()
                ->send();

            Log::info('Backup output: ' . $output);

            $this->dispatch('$refresh');

        } catch (Exception $e) {
            Notification::make()
                ->title('Backup Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            Log::error('Backup error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    public function downloadBackup(string $path)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');

        if ( ! $disk->exists($path)) {
            Notification::make()
                ->title('File Not Found')
                ->body('The backup file could not be found.')
                ->danger()
                ->send();

            return redirect()->route('backups.index');
        }

        return response()->streamDownload(function () use ($disk, $path): void {
            echo $disk->get($path);
        }, basename($path));
    }

    public function deleteBackup(string $path): void
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');

        if ($disk->exists($path)) {
            $disk->delete($path);

            Notification::make()
                ->title('Backup Deleted')
                ->body('The backup has been deleted successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('File Not Found')
                ->body('The backup file could not be found.')
                ->warning()
                ->send();
        }

        $this->dispatch('$refresh');
    }

    public function deleteMultiple(Collection $records): void
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $count = 0;

        foreach ($records as $record) {
            if ($disk->exists($record['path'])) {
                $disk->delete($record['path']);
                $count++;
            }
        }

        Notification::make()
            ->title('Backups Deleted')
            ->body("{$count} backup(s) deleted successfully.")
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function getTotalSize(): string
    {
        $totalBytes = $this->getBackups()->sum('size_bytes');

        return $this->formatBytes($totalBytes);
    }

    public function getLastBackupTime(): ?string
    {
        $lastBackup = $this->getBackups()->first();

        if ( ! $lastBackup) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($lastBackup['date'])->diffForHumans();
    }

    public function getDiskSpaceWarning(): ?string
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
            $path = $disk->path('');

            $totalSpace = disk_total_space($path);
            $freeSpace = disk_free_space($path);

            if ($totalSpace === false || $freeSpace === false) {
                return null;
            }

            $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

            if ($usedPercent > 90) {
                return __('Critical: Disk space is running low (:percent% used). Please free up space or delete old backups.', [
                    'percent' => round($usedPercent, 1),
                ]);
            }

            if ($usedPercent > 80) {
                return __('Warning: Disk space usage is high (:percent% used). Consider cleaning up old backups.', [
                    'percent' => round($usedPercent, 1),
                ]);
            }

            return null;
        } catch (Exception $e) {
            Log::warning('Failed to check disk space: ' . $e->getMessage());

            return null;
        }
    }

    public function render(): View
    {
        return view('livewire.backup-manager');
    }

    protected function getBackups(): Collection
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $backupPath = config('backup.backup.name');

        if ( ! $disk->exists($backupPath)) {
            return collect();
        }

        $files = $disk->files($backupPath);

        return collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->map(fn($file) => [
                'name' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes($disk->size($file)),
                'size_bytes' => $disk->size($file),
                'date' => $disk->lastModified($file),
                'created_at' => date('Y-m-d H:i:s', $disk->lastModified($file)),
            ])
            ->sortByDesc('date')
            ->values();
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
