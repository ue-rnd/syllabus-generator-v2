<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\SecurityAuditLogResource;
use App\Models\SecurityAuditLog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSecurityAuditLogs extends ListRecords
{
    protected static string $resource = SecurityAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportLogs')
                ->label('Export Logs')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    // Export functionality would go here
                    $this->notify('success', 'Export functionality to be implemented');
                })
                ->visible(fn () => auth()->user()->can('export reports')),

            Actions\Action::make('clearOldLogs')
                ->label('Clear Old Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear Old Audit Logs')
                ->modalDescription('This will delete audit logs older than 90 days. This action cannot be undone.')
                ->action(function () {
                    $deleted = SecurityAuditLog::where('created_at', '<', now()->subDays(90))->delete();
                    $this->notify('success', "Deleted {$deleted} old audit logs");
                })
                ->visible(fn () => auth()->user()->can('manage system settings')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Logs')
                ->badge(SecurityAuditLog::count()),

            'recent' => Tab::make('Recent (7 days)')
                ->modifyQueryUsing(fn (Builder $query) => $query->recent(7))
                ->badge(SecurityAuditLog::recent(7)->count()),

            'high_severity' => Tab::make('High Severity')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('severity', ['high', 'critical']))
                ->badge(SecurityAuditLog::whereIn('severity', ['high', 'critical'])->count())
                ->badgeColor('danger'),

            'login_events' => Tab::make('Login Events')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('event_type', ['login_success', 'login_failed', 'logout']))
                ->badge(SecurityAuditLog::whereIn('event_type', ['login_success', 'login_failed', 'logout'])->count()),

            'security_events' => Tab::make('Security Events')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('event_type', ['suspicious_activity', 'account_locked', 'permission_denied', 'data_breach']))
                ->badge(SecurityAuditLog::whereIn('event_type', ['suspicious_activity', 'account_locked', 'permission_denied', 'data_breach'])->count())
                ->badgeColor('warning'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [25, 50, 100, 200];
    }

    public function getTitle(): string
    {
        return 'Security Audit Logs';
    }

    public function getHeading(): string
    {
        return 'Security Audit Logs';
    }

    public function getSubheading(): ?string
    {
        return 'Monitor and review security events and user activities.';
    }
}