<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Tables;

use App\Constants\SyllabusConstants;
use App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Actions\SyllabusApprovalActions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
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
                    ->formatStateUsing(fn (string $state): string => SyllabusConstants::getStatusOptions()[$state])
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

                BadgeColumn::make('suggestions_count')
                    ->label('Suggestions')
                    ->formatStateUsing(function ($record): string {
                        $count = $record->suggestions()->count();
                        $pending = $record->pendingSuggestions()->count();

                        return $pending > 0 ? "{$pending} pending" : ($count > 0 ? "{$count} total" : '0');
                    })
                    ->color(function ($record): string {
                        $pending = $record->pendingSuggestions()->count();

                        return $pending > 0 ? 'warning' : 'gray';
                    })
                    ->visible(fn (): bool => auth()->user()->position !== 'superadmin')
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
                    ->visible(fn ($record): bool => $record->canBeDirectlyEditedBy(auth()->user())),
                Action::make('suggest_changes')
                    ->label('Suggest Changes')
                    ->icon('heroicon-o-light-bulb')
                    ->color('info')
                    ->url(fn ($record): string => route('filament.admin.academic.resources.syllabi.edit', $record).'?mode=suggest')
                    ->visible(fn ($record): bool => $record->canSuggestChanges(auth()->user()) &&
                        ! $record->canBeDirectlyEditedBy(auth()->user())
                    ),
                Action::make('view_suggestions')
                    ->label('View Suggestions')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn ($record): string => route('filament.admin.academic.resources.syllabus-suggestions.index').'?tableFilters[syllabus][value]='.$record->id)
                    ->visible(fn ($record): bool => $record->canViewSuggestions(auth()->user()) &&
                        $record->suggestions()->count() > 0
                    )
                    ->badge(fn ($record): ?string => $record->pendingSuggestions()->count() > 0 ?
                        (string) $record->pendingSuggestions()->count() : null
                    ),
                ReplicateAction::make('duplicate')
                    ->label('Duplicate')
                    ->beforeReplicaSaved(function (array $data): array {
                        $data['name'] = $data['name'].' (Copy)';
                        $data['status'] = 'draft';
                        $data['submitted_at'] = null;
                        $data['dept_chair_reviewed_at'] = null;
                        $data['assoc_dean_reviewed_at'] = null;
                        $data['dean_approved_at'] = null;
                        $data['approval_history'] = [];
                        $data['rejection_comments'] = null;
                        $data['rejected_by_role'] = null;
                        $data['rejected_at'] = null;
                        $data['reviewed_by'] = null;
                        $data['recommending_approval'] = null;
                        $data['approved_by'] = null;
                        $data['parent_syllabus_id'] = null;

                        return $data;
                    }),
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
