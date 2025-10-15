<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Schemas;

use App\Models\DatabaseBackup;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DatabaseBackupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Backup Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Backup Name')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                        TextEntry::make('backup_type')
                            ->label('Backup Type')
                            ->badge()
                            ->color(fn ($record) => $record->getBackupTypeColorAttribute())
                            ->formatStateUsing(fn ($state) => DatabaseBackup::getBackupTypeOptions()[$state] ?? ucfirst($state)),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($record) => $record->getStatusColorAttribute())
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                    ])
                    ->columnSpanFull(),

                Section::make('File Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('File Path')
                            ->placeholder('-'),
                        TextEntry::make('formatted_file_size')
                            ->label('File Size')
                            ->placeholder('-'),
                        IconEntry::make('file_exists')
                            ->label('File Exists')
                            ->boolean()
                            ->state(fn ($record) => $record->getFileExists()),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Tables Included')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('tables_count')
                            ->label('Total Tables')
                            ->state(fn ($record) => is_array($record->tables_included) ? count($record->tables_included) : 0),

                        TextEntry::make('tables_list')
                            ->label('Tables')
                            ->state(function ($record) {
                                if (empty($record->tables_included) || ! is_array($record->tables_included)) {
                                    return 'No tables specified';
                                }

                                // Limit to first 20 tables to prevent memory issues
                                $tables = array_slice($record->tables_included, 0, 20);
                                $mainTables = DatabaseBackup::getMainTables();

                                $tableList = collect($tables)->map(function ($table) use ($mainTables) {
                                    if (is_array($table)) {
                                        $tableName = key($table);
                                    } else {
                                        $tableName = $table;
                                    }

                                    return $mainTables[$tableName] ?? $tableName;
                                })->join(', ');

                                $remaining = count($record->tables_included) - count($tables);
                                if ($remaining > 0) {
                                    $tableList .= " ... and {$remaining} more";
                                }

                                return $tableList;
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => ! empty($record->tables_included)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Error Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->color('danger')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->status === 'failed' && ! empty($record->error_message))
                    ->columnSpanFull(),

                Section::make('Metadata')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('creator.name')
                            ->label('Created By')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
