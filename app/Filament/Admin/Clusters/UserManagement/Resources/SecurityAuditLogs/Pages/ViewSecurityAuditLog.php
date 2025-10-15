<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\SecurityAuditLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSecurityAuditLog extends ViewRecord
{
    protected static string $resource = SecurityAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsInvestigated')
                ->label('Mark as Investigated')
                ->icon('heroicon-o-magnifying-glass')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'logged' && auth()->user()->can('manage system settings'))
                ->action(function () {
                    $this->record->update(['status' => 'investigating']);
                    $this->notify('success', 'Audit log marked as under investigation');
                }),

            Actions\Action::make('markAsResolved')
                ->label('Mark as Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['logged', 'investigating']) && auth()->user()->can('manage system settings'))
                ->action(function () {
                    $this->record->update(['status' => 'resolved']);
                    $this->notify('success', 'Audit log marked as resolved');
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('manage system settings')),
        ];
    }


    public function getTitle(): string
    {
        return 'Security Audit Log Details';
    }
}