<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\SecurityAuditLogs\Tables;

use App\Models\SecurityAuditLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SecurityAuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date/Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('event_type')
                    ->label('Event Type')
                    ->badge()
                    ->color(fn ($record) => $record->getEventTypeColorAttribute())
                    ->formatStateUsing(fn ($state) => SecurityAuditLog::getEventTypeOptions()[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn ($record) => $record->getSeverityColorAttribute())
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System/Unknown'),

                TextColumn::make('event_description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->placeholder('Unknown')
                    ->copyable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'logged' => 'success',
                        'investigating' => 'warning',
                        'resolved' => 'success',
                        'escalated' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_type')
                    ->label('Event Type')
                    ->options(SecurityAuditLog::getEventTypeOptions())
                    ->searchable(),

                SelectFilter::make('severity')
                    ->label('Severity')
                    ->options(SecurityAuditLog::getSeverityOptions())
                    ->default('high'),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('recent')
                    ->label('Recent (Last 7 days)')
                    ->query(fn (Builder $query): Builder => $query->recent(7))
                    ->default(),

                Filter::make('high_severity')
                    ->label('High Severity')
                    ->query(fn (Builder $query): Builder => $query->whereIn('severity', ['high', 'critical'])),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Audit Logs')
                        ->modalDescription('Are you sure you want to delete these audit logs? This action cannot be undone.')
                        ->visible(fn () => auth()->user()->can('manage system settings')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Auto-refresh every 30 seconds
            ->striped()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->recordUrl(fn ($record) => route('filament.admin.resources.security-audit-logs.view', $record));
    }
}
