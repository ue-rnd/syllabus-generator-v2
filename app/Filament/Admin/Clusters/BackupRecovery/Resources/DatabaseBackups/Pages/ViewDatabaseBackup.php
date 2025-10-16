<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages;

use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\DatabaseBackupResource;
use App\Models\DatabaseBackup;
use App\Services\DatabaseBackupService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDatabaseBackup extends ViewRecord
{
    protected static string $resource = DatabaseBackupResource::class;

    // Eager load relationships to prevent N+1 queries
    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key)
            ->load('creator');
    }

    protected function getHeaderActions(): array
    {
        /** @var DatabaseBackup $record */
        $record = $this->record;

        return [
            Actions\Action::make('download')
                ->label('Download Backup')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn () => $record->status === 'completed' && $record->getFileExists())
                ->url(fn () => route('admin.backups.download', $record))
                ->openUrlInNewTab(),

            Actions\Action::make('restore')
                ->label('Restore Database')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $record->status === 'completed' && $record->getFileExists())
                ->requiresConfirmation()
                ->modalHeading('Restore Database from Backup')
                ->modalDescription('This will restore the database from this backup. All current data will be replaced with the data from this backup. This action cannot be undone. Are you sure you want to proceed?')
                ->modalSubmitActionLabel('Restore Database')
                ->action(function () use ($record) {
                    try {
                        $service = app(DatabaseBackupService::class);
                        $service->restoreFromBackup($record);

                        Notification::make()
                            ->title('Database Restored')
                            ->body('Database has been successfully restored from backup.')
                            ->success()
                            ->send();

                        $this->redirect(static::getResource()::getUrl('index'));

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Restore Failed')
                            ->body('Failed to restore database: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make()
                ->modalHeading('Delete Backup')
                ->modalDescription('This will permanently delete the backup file and record. This action cannot be undone.')
                ->after(function () use ($record) {
                    $record->deleteFile();
                }),
        ];
    }

    public function getTitle(): string
    {
        /** @var DatabaseBackup $record */
        $record = $this->record;

        return 'Backup Details: ' . $record->name;
    }
}
