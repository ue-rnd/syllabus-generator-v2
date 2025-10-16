<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs;

use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Pages\ListSecurityAuditLogs;
use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Pages\ViewSecurityAuditLog;
use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Tables\SecurityAuditLogsTable;
use App\Filament\Admin\Clusters\UserManagement\UserManagement;
use App\Models\SecurityAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SecurityAuditLogResource extends Resource
{
    protected static ?string $model = SecurityAuditLog::class;

    protected static ?string $cluster = UserManagement::class;

    protected static ?int $navigationSort = 40;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $navigationLabel = 'Security Audit Logs';

    protected static ?string $recordTitleAttribute = 'event_description';

    public static function table(Table $table): Table
    {
        return SecurityAuditLogsTable::configure($table);
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
            'index' => ListSecurityAuditLogs::route('/'),
            'view' => ViewSecurityAuditLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Logs are created automatically, not manually
    }

    public static function canEdit($record): bool
    {
        return false; // Logs should not be editable for integrity
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('manage system settings'); // Only super admins
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('manage system settings');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view system logs');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view system logs');
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::getModel();
        /** @var class-string<\App\Models\SecurityAuditLog> $modelClass */

        return $modelClass::bySeverity('high')->recent(7)->count() > 0
            ? (string) $modelClass::bySeverity('high')->recent(7)->count()
            : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $modelClass = static::getModel();
        /** @var class-string<\App\Models\SecurityAuditLog> $modelClass */
        $highSeverityCount = $modelClass::bySeverity('high')->recent(7)->count();

        if ($highSeverityCount > 10) {
            return 'danger';
        } elseif ($highSeverityCount > 5) {
            return 'warning';
        }

        return null;
    }
}
