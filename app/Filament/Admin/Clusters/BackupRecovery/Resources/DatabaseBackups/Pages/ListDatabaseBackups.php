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
                ->schema([
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

                        $content = '<div class="space-y-6">';

                        // Overview Section
                        $content .= '<div class="grid grid-cols-2 gap-4">';

                        // Total backups
                        if (isset($stats['total_backups'])) {
                            $content .= '<div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">';
                            $content .= '<span class="font-medium text-gray-700 dark:text-gray-300">Total backups:</span>';
                            $content .= '<span class="text-lg font-semibold text-gray-900 dark:text-white">' . $stats['total_backups'] . '</span>';
                            $content .= '</div>';
                        }

                        // Successful backups
                        if (isset($stats['successful_backups'])) {
                            $content .= '<div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">';
                            $content .= '<span class="font-medium text-green-700 dark:text-green-300">Successful backups:</span>';
                            $content .= '<span class="text-lg font-semibold text-green-900 dark:text-green-100">' . $stats['successful_backups'] . '</span>';
                            $content .= '</div>';
                        }

                        // Failed backups
                        if (isset($stats['failed_backups'])) {
                            $content .= '<div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">';
                            $content .= '<span class="font-medium text-red-700 dark:text-red-300">Failed backups:</span>';
                            $content .= '<span class="text-lg font-semibold text-red-900 dark:text-red-100">' . $stats['failed_backups'] . '</span>';
                            $content .= '</div>';
                        }

                        // Total size
                        if (isset($stats['total_size'])) {
                            $content .= '<div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">';
                            $content .= '<span class="font-medium text-blue-700 dark:text-blue-300">Total size:</span>';
                            $content .= '<span class="text-lg font-semibold text-blue-900 dark:text-blue-100">' . number_format($stats['total_size'] / 1024, 2) . ' MB</span>';
                            $content .= '</div>';
                        }

                        $content .= '</div>';

                        // Latest Backup Section
                        if (isset($stats['latest']) && is_array($stats['latest'])) {
                            $latest = $stats['latest'];
                            $content .= '<div class="border-t border-gray-200 dark:border-gray-700 pt-4">';
                            $content .= '<h4 class="font-semibold text-gray-900 dark:text-white mb-3">Latest Backup</h4>';
                            $content .= '<div class="space-y-2 text-sm">';

                            if (isset($latest['name'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Name:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . htmlspecialchars($latest['name']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($latest['created_at'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Created:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . htmlspecialchars($latest['created_at']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($latest['backup_type'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Type:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white capitalize">' . htmlspecialchars($latest['backup_type']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($latest['tables_included']) && is_array($latest['tables_included'])) {
                                $tableCount = count($latest['tables_included']);
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Tables:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . $tableCount . ' table' . ($tableCount !== 1 ? 's' : '') . '</span>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';
                            $content .= '</div>';
                        }

                        // Oldest Backup Section
                        if (isset($stats['oldest']) && is_array($stats['oldest'])) {
                            $oldest = $stats['oldest'];
                            $content .= '<div class="border-t border-gray-200 dark:border-gray-700 pt-4">';
                            $content .= '<h4 class="font-semibold text-gray-900 dark:text-white mb-3">Oldest Backup</h4>';
                            $content .= '<div class="space-y-2 text-sm">';

                            if (isset($oldest['name'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Name:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . htmlspecialchars($oldest['name']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($oldest['created_at'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Created:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . htmlspecialchars($oldest['created_at']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($oldest['backup_type'])) {
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Type:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white capitalize">' . htmlspecialchars($oldest['backup_type']) . '</span>';
                                $content .= '</div>';
                            }

                            if (isset($oldest['tables_included']) && is_array($oldest['tables_included'])) {
                                $tableCount = count($oldest['tables_included']);
                                $content .= '<div class="flex justify-between">';
                                $content .= '<span class="text-gray-600 dark:text-gray-400">Tables:</span>';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . $tableCount . ' table' . ($tableCount !== 1 ? 's' : '') . '</span>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';
                            $content .= '</div>';
                        }

                        $content .= '</div>';

                        return new \Illuminate\Support\HtmlString($content);
                    } catch (\Exception $e) {
                        return new \Illuminate\Support\HtmlString('<div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400">Unable to load statistics: ' . htmlspecialchars($e->getMessage()) . '</div>');
                    }
                })
                ->modalWidth('xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false),
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
