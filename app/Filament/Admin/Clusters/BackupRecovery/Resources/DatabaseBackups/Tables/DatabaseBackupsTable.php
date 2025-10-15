<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Tables;

use App\Models\DatabaseBackup;
use App\Services\DatabaseBackupService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DatabaseBackupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Backup Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->description)
                    ->limit(50),

                TextColumn::make('backup_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($record) => $record->getBackupTypeColorAttribute())
                    ->formatStateUsing(fn ($state) => DatabaseBackup::getBackupTypeOptions()[$state] ?? ucfirst($state))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->getStatusColorAttribute())
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('formatted_file_size')
                    ->label('File Size')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('file_size', $direction);
                    }),

                TextColumn::make('tables_count')
                    ->label('Tables')
                    ->formatStateUsing(fn ($record) => is_array($record->tables_included) ? count($record->tables_included) : 0)
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(30)
                    ->visible(fn ($record) => $record?->status === 'failed')
                    ->color('danger'),
            ])
            ->filters([
                SelectFilter::make('backup_type')
                    ->label('Backup Type')
                    ->options(DatabaseBackup::getBackupTypeOptions()),

                SelectFilter::make('status')
                    ->options(DatabaseBackup::getStatusOptions()),

                SelectFilter::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'completed' && $record->getFileExists())
                    ->url(fn ($record) => route('admin.backups.download', $record))
                    ->openUrlInNewTab(),

                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'completed' && $record->getFileExists())
                    ->requiresConfirmation()
                    ->modalHeading('Restore Database')
                    ->modalDescription('This will restore the database from this backup. Current data will be replaced. Are you sure?')
                    ->modalSubmitActionLabel('Restore Database')
                    ->action(function ($record) {
                        try {
                            $service = app(DatabaseBackupService::class);
                            $service->restoreFromBackup($record);

                            Notification::make()
                                ->title('Database Restored')
                                ->body('Database has been successfully restored from backup.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Restore Failed')
                                ->body('Failed to restore database: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                DeleteAction::make()
                    ->modalHeading('Delete Backup')
                    ->modalDescription('This will permanently delete the backup file and record. This action cannot be undone.')
                    ->after(function ($record) {
                        $record->deleteFile();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                $record->deleteFile();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Refresh every 30 seconds to show status updates
            ->recordUrl(fn ($record) => route('filament.admin.backup-recovery.resources.database-backups.view', $record));
    }
}
