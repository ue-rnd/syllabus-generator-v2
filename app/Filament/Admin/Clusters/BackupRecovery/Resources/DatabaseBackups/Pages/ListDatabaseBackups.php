<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages;

use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\DatabaseBackupResource;
use App\Models\DatabaseBackup;
use App\Services\DatabaseBackupService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDatabaseBackups extends ListRecords
{
    protected static string $resource = DatabaseBackupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Backup'),

            Actions\Action::make('import_backup')
                ->label('Import Backup')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('backup_file')
                        ->label('Backup File')
                        ->required()
                        ->acceptedFileTypes(['application/sql', 'text/plain', '.sql'])
                        ->maxSize(100 * 1024) // 100MB
                        ->disk('temp')
                        ->directory('imports')
                        ->helperText('Upload a .sql backup file to restore'),
                ])
                ->action(function (array $data) {
                    try {
                        $service = app(DatabaseBackupService::class);
                        $service->importSqlFile($data['backup_file'], $data['backup_file']);

                        Notification::make()
                            ->title('Backup Imported')
                            ->body('Database has been successfully restored from the imported backup.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body('Failed to import backup: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('cleanup_old')
                ->label('Cleanup Old Backups')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Old Backups')
                ->modalDescription('This will delete backup files older than 30 days. Are you sure?')
                ->action(function () {
                    try {
                        $service = app(DatabaseBackupService::class);
                        $deletedCount = $service->cleanupOldBackups(30);

                        Notification::make()
                            ->title('Cleanup Completed')
                            ->body("Deleted {$deletedCount} old backup files.")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Cleanup Failed')
                            ->body('Failed to cleanup backups: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('view_statistics')
                ->label('Statistics')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->modalHeading('Backup Statistics')
                ->modalContent(function () {
                    try {
                        $service = app(DatabaseBackupService::class);
                        $stats = $service->getBackupStatistics();
                        
                        $content = '<div class="space-y-4">';
                        foreach ($stats as $key => $value) {
                            $content .= '<div class="flex justify-between"><span class="font-medium">' . ucfirst(str_replace('_', ' ', $key)) . ':</span><span>' . $value . '</span></div>';
                        }
                        $content .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($content);
                    } catch (\Exception $e) {
                        return new \Illuminate\Support\HtmlString('<div class="text-red-600">Unable to load statistics: ' . $e->getMessage() . '</div>');
                    }
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Backups')
                ->badge(DatabaseBackup::count()),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->successful())
                ->badge(DatabaseBackup::successful()->count()),

            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->failed())
                ->badge(DatabaseBackup::failed()->count())
                ->badgeColor('danger'),

            'full' => Tab::make('Full Backups')
                ->modifyQueryUsing(fn (Builder $query) => $query->byType('full'))
                ->badge(DatabaseBackup::byType('full')->count()),

            'selective' => Tab::make('Selective')
                ->modifyQueryUsing(fn (Builder $query) => $query->byType('selective'))
                ->badge(DatabaseBackup::byType('selective')->count()),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }

    public function getTitle(): string
    {
        return 'Database Backups';
    }

    public function getHeading(): string
    {
        return 'Database Backups';
    }

    public function getSubheading(): ?string
    {
        return 'Create, manage, and restore database backups to protect your data.';
    }
}