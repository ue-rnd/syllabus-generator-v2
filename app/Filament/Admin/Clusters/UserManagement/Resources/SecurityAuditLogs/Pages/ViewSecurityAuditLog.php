<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\SecurityAuditLogResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Event Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('event_type')
                                    ->label('Event Type')
                                    ->badge()
                                    ->color(fn ($record) => $record->getEventTypeColorAttribute())
                                    ->formatStateUsing(fn ($state) => $this->record->getEventTypeOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state))),

                                TextEntry::make('severity')
                                    ->label('Severity')
                                    ->badge()
                                    ->color(fn ($record) => $record->getSeverityColorAttribute())
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                            ]),

                        TextEntry::make('event_description')
                            ->label('Description')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date/Time')
                                    ->dateTime('F j, Y \a\t g:i A'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'logged' => 'gray',
                                        'investigating' => 'warning',
                                        'resolved' => 'success',
                                        'escalated' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                            ]),
                    ]),

                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User Name')
                                    ->placeholder('System/Unknown'),

                                TextEntry::make('user.email')
                                    ->label('User Email')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.position')
                                    ->label('User Position')
                                    ->formatStateUsing(fn ($state, $record) => $record->user?->getPositionTitleAttribute() ?? 'N/A'),

                                TextEntry::make('user.primary_role')
                                    ->label('User Role')
                                    ->formatStateUsing(fn ($state, $record) => $record->user ? ucwords(str_replace('_', ' ', $record->user->getPrimaryRoleAttribute())) : 'N/A'),
                            ]),
                    ])
                    ->visible(fn () => $this->record->user_id !== null),

                Section::make('Technical Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->placeholder('Unknown')
                                    ->copyable(),

                                TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->placeholder('Unknown')
                                    ->limit(100),
                            ]),
                    ]),

                Section::make('Additional Metadata')
                    ->schema([
                        TextEntry::make('metadata')
                            ->label('Metadata')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'No additional metadata')
                            ->placeholder('No additional metadata')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn () => empty($this->record->metadata)),

                Section::make('Related Information')
                    ->schema([
                        TextEntry::make('related_logs_count')
                            ->label('Related Logs (Same User, Last 24h)')
                            ->formatStateUsing(function () {
                                if (!$this->record->user_id) {
                                    return 'N/A';
                                }

                                $count = \App\Models\SecurityAuditLog::where('user_id', $this->record->user_id)
                                    ->where('created_at', '>=', now()->subDay())
                                    ->where('id', '!=', $this->record->id)
                                    ->count();

                                return $count > 0 ? $count : 'None';
                            }),

                        TextEntry::make('ip_logs_count')
                            ->label('Logs from Same IP (Last 24h)')
                            ->formatStateUsing(function () {
                                if (!$this->record->ip_address) {
                                    return 'N/A';
                                }

                                $count = \App\Models\SecurityAuditLog::where('ip_address', $this->record->ip_address)
                                    ->where('created_at', '>=', now()->subDay())
                                    ->where('id', '!=', $this->record->id)
                                    ->count();

                                return $count > 0 ? $count : 'None';
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public function getTitle(): string
    {
        return 'Security Audit Log Details';
    }
}