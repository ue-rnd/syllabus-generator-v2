<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Tables;

use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Actions\SyllabusApprovalActions;
use App\Constants\SyllabusConstants;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->formatStateUsing(fn (string $state): string => SyllabusConstants::getStatusOptions()[$state] ?? 'Unknown')
                    ->color(fn (string $state): string => SyllabusConstants::getStatusColor($state))
                    ->sortable(),
                
                TextColumn::make('version')
                    ->label('Ver.')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not submitted')
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SyllabusConstants::getStatusOptions())
                    ->multiple()
                    ->preload(),
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record): bool => 
                        in_array($record->status, ['draft', 'for_revisions']) &&
                        ($record->principal_prepared_by === auth()->id() || 
                         collect($record->prepared_by)->contains('user_id', auth()->id()))
                    ),
                SyllabusApprovalActions::submitForApproval(),
                SyllabusApprovalActions::approve(),
                SyllabusApprovalActions::reject(),
                SyllabusApprovalActions::createRevision(),
                SyllabusApprovalActions::viewApprovalHistory(),
                SyllabusApprovalActions::viewPdf(),
                SyllabusApprovalActions::exportPdf(),
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
