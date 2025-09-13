<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Tables;

use App\Constants\SyllabusConstants;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SyllabiTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => SyllabusConstants::STATUSES[$state] ?? 'Unknown')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending_approval',
                        'danger' => 'rejected',
                        'info' => 'for_revisions',
                        'success' => 'approved',
                    ])
                    ->sortable(),
                
                TextColumn::make('approval_status')
                    ->label('Status')
                    ->sortable(),
                
                TextColumn::make('principalPreparer.name')
                    ->label('Principal Preparer')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
