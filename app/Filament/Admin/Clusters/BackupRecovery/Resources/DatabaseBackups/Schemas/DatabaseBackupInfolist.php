<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Schemas;

use App\Models\DatabaseBackup;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
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
                        RepeatableEntry::make('tables_included')
                            ->label('')
                            ->schema([
                                TextEntry::make('table')
                                    ->state(function ($state) {
                                        // If tables_included is an associative array with keys as table names
                                        if (is_array($state) && !is_numeric(key($state))) {
                                            return key($state);
                                        }
                                        // If it's just a simple array of table names
                                        return $state;
                                    }),
                                TextEntry::make('display_name')
                                    ->state(function ($state, $record) {
                                        // Get the display name from the main tables mapping
                                        $mainTables = DatabaseBackup::getMainTables();
                                        if (is_array($state) && !is_numeric(key($state))) {
                                            return $mainTables[key($state)] ?? key($state);
                                        }
                                        return $mainTables[$state] ?? $state;
                                    }),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->tables_included)),
                        
                        TextEntry::make('tables_count')
                            ->label('Total Tables')
                            ->state(fn ($record) => is_array($record->tables_included) ? count($record->tables_included) : 0)
                            ->visible(fn ($record) => empty($record->tables_included))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Error Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->color('danger')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->status === 'failed' && !empty($record->error_message))
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