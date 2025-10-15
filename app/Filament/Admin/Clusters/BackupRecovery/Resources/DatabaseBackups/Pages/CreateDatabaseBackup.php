<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages;

use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\DatabaseBackupResource;
use App\Services\DatabaseBackupService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDatabaseBackup extends CreateRecord
{
    protected static string $resource = DatabaseBackupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default tables for full backup
        if ($data['backup_type'] === 'full') {
            $data['tables_included'] = array_keys(\App\Models\DatabaseBackup::getMainTables());
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            $service = app(DatabaseBackupService::class);
            $backup = $this->record;

            if ($backup->backup_type === 'full') {
                $service->createFullBackup($backup->name, $backup->description);
            } else {
                $service->createSelectiveBackup(
                    $backup->tables_included,
                    $backup->name,
                    $backup->description
                );
            }

            // Delete the placeholder record since the service creates the actual one
            $backup->delete();

            Notification::make()
                ->title('Backup Created')
                ->body('Database backup has been created successfully.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup Failed')
                ->body('Failed to create backup: ' . $e->getMessage())
                ->danger()
                ->send();
        }

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Create Database Backup';
    }

    public function getHeading(): string
    {
        return 'Create Database Backup';
    }

    public function getSubheading(): ?string
    {
        return 'Configure and create a new database backup to protect your data.';
    }
}
