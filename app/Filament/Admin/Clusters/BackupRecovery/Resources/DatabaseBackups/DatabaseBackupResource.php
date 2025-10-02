<?php

namespace App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups;

use App\Filament\Admin\Clusters\BackupRecovery\BackupRecovery;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages\CreateDatabaseBackup;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages\ListDatabaseBackups;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Pages\ViewDatabaseBackup;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Schemas\DatabaseBackupForm;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Schemas\DatabaseBackupInfolist;
use App\Filament\Admin\Clusters\BackupRecovery\Resources\DatabaseBackups\Tables\DatabaseBackupsTable;
use App\Models\DatabaseBackup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DatabaseBackupResource extends Resource
{
    protected static ?string $model = DatabaseBackup::class;

    protected static ?string $cluster = BackupRecovery::class;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static ?string $navigationLabel = 'Database Backups';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DatabaseBackupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DatabaseBackupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DatabaseBackupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDatabaseBackups::route('/'),
            'create' => CreateDatabaseBackup::route('/create'),
            'view' => ViewDatabaseBackup::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }

    public static function canEdit($record): bool
    {
        return false; // Backups should not be editable
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('manage system settings') ||
               auth()->user()->can('manage backups');
    }

    public static function getNavigationBadge(): ?string
    {
        $failedCount = static::getModel()::failed()->count();
        return $failedCount > 0 ? (string) $failedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'danger' : null;
    }
}