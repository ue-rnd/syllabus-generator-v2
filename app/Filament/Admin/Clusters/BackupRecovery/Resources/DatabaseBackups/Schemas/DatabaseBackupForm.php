<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Schemas;

use App\Models\DatabaseBackup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DatabaseBackupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Backup Configuration')
                    ->description('Configure your database backup settings')
                    ->schema([
                        TextInput::make('name')
                            ->label('Backup Name')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'Database Backup - ' . now()->format('Y-m-d H:i'))
                            ->helperText('Give your backup a descriptive name'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Optional description of what this backup contains'),

                        Radio::make('backup_type')
                            ->label('Backup Type')
                            ->required()
                            ->options([
                                'full' => 'Full Database Backup - All main tables',
                                'selective' => 'Selective Backup - Choose specific tables',
                            ])
                            ->default('full')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'full') {
                                    $set('tables_included', array_keys(DatabaseBackup::getMainTables()));
                                } else {
                                    $set('tables_included', []);
                                }
                            }),

                        CheckboxList::make('tables_included')
                            ->label('Tables to Include')
                            ->options(DatabaseBackup::getMainTables())
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable()
                            ->required()
                            ->visible(fn (callable $get) => $get('backup_type') === 'selective')
                            ->helperText('Select the tables you want to include in the backup'),

                        Hidden::make('created_by')
                            ->default(auth()->id()),

                        Hidden::make('status')
                            ->default('pending'),
                    ]),

                Section::make('Important Information')
                    ->description('Please read before proceeding')
                    ->schema([
                        Placeholder::make('backup_info')
                            ->content('
                                <div class="space-y-2 text-sm">
                                    <p><strong>What happens when I create a backup?</strong></p>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>A complete SQL dump of selected tables will be created</li>
                                        <li>The backup file will be stored securely on the server</li>
                                        <li>You can download the backup file for external storage</li>
                                        <li>Large databases may take several minutes to backup</li>
                                    </ul>

                                    <p class="mt-4"><strong>Security Note:</strong></p>
                                    <p>Backup files contain sensitive data. Keep them secure and delete old backups when no longer needed.</p>
                                </div>
                            ')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}