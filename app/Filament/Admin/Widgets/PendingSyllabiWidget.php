<?php

namespace App\Filament\Admin\Widgets;

use App\Constants\SyllabusConstants;
use App\Models\Syllabus;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingSyllabiWidget extends BaseWidget
{
    protected static ?string $heading = 'Syllabi Pending Your Approval';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Syllabus Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Syllabus $record): string => $record->course->name ?? 'No course'),

                Tables\Columns\TextColumn::make('course.code')
                    ->label('Course Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => SyllabusConstants::getStatusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => SyllabusConstants::getStatusColor($state)),

                Tables\Columns\TextColumn::make('principalPreparer.full_name')
                    ->label('Prepared By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Not submitted'),

                Tables\Columns\TextColumn::make('version')
                    ->label('Ver.')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options($this->getAvailableStatuses())
                    ->default($this->getDefaultStatusFilter()),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Syllabus $record): string => route('filament.admin.academic.resources.syllabi.view', $record)),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Syllabus $record) => $record->canApprove($user))
                    ->form(function (Syllabus $record) use ($user) {
                        // Show comments field for associate dean, dean, QA, and superadmin
                        if ($user->position === 'associate_dean' ||
                            $user->position === 'dean' ||
                            $user->position === 'qa_representative' ||
                            $user->position === 'superadmin') {
                            return [
                                Forms\Components\Textarea::make('comments')
                                    ->label('Approval Comments (Optional)')
                                    ->rows(3)
                                    ->placeholder('Add any comments about this approval...'),
                            ];
                        }

                        return [];
                    })
                    ->modalHeading(fn (Syllabus $record) => 'Approve Syllabus: '.$record->name)
                    ->modalDescription('Please review and approve this syllabus.')
                    ->modalSubmitActionLabel('Approve')
                    ->action(function (Syllabus $record, array $data) use ($user) {
                        $comments = $data['comments'] ?? null;
                        $record->approve($user, $comments);
                        $this->dispatch('refresh');
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Syllabus $record) => $record->canReject($user))
                    ->form([
                        Forms\Components\Textarea::make('comments')
                            ->label('Rejection Comments')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please provide reasons for rejection...'),
                    ])
                    ->modalHeading(fn (Syllabus $record) => 'Reject Syllabus: '.$record->name)
                    ->modalDescription('Please provide clear reasons for rejecting this syllabus.')
                    ->modalSubmitActionLabel('Reject')
                    ->action(function (Syllabus $record, array $data) use ($user) {
                        $record->reject($user, $data['comments']);
                        $this->dispatch('refresh');
                    }),
            ])
            ->emptyStateHeading('No pending syllabi')
            ->emptyStateDescription('There are no syllabi pending your approval at this time.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->defaultSort('submitted_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        $query = Syllabus::query()
            ->with(['course', 'principalPreparer'])
            ->whereNotNull('submitted_at');

        // Filter based on user's position and approval permissions
        if ($user->position === 'superadmin') {
            // Superadmin can see all pending syllabi
            $query->whereIn('status', ['pending_approval', 'dept_chair_review', 'assoc_dean_review', 'dean_review', 'qa_review']);
        } elseif ($user->position === 'qa_representative') {
            // QA representatives see syllabi in qa_review status
            $query->where('status', 'qa_review');
        } elseif ($user->position === 'department_chair') {
            // Department chairs see syllabi pending initial review and their department's syllabi
            $query->whereIn('status', ['pending_approval', 'dept_chair_review'])
                ->where(function ($q) use ($user) {
                    // Syllabi from courses in their department
                    $q->whereHas('course.programs.department', function ($deptQuery) use ($user) {
                        $deptQuery->where('department_chair_id', $user->id);
                    });
                });
        } elseif ($user->position === 'associate_dean') {
            // Associate deans see syllabi in assoc_dean_review status from their college
            $query->where('status', 'assoc_dean_review')
                ->whereHas('course.college', function ($collegeQuery) use ($user) {
                    $collegeQuery->where('associate_dean_id', $user->id);
                });
        } elseif ($user->position === 'dean') {
            // Deans see syllabi in dean_review status from their college
            $query->where('status', 'dean_review')
                ->whereHas('course.college', function ($collegeQuery) use ($user) {
                    $collegeQuery->where('dean_id', $user->id);
                });
        } else {
            // Regular faculty see only syllabi they prepared that are pending
            $query->where('principal_prepared_by', $user->id)
                ->whereIn('status', ['pending_approval', 'dept_chair_review', 'assoc_dean_review', 'dean_review', 'qa_review']);
        }

        return $query;
    }

    protected function getAvailableStatuses(): array
    {
        $user = auth()->user();

        if ($user->position === 'superadmin') {
            return [
                'pending_approval' => 'Awaiting Review',
                'dept_chair_review' => 'Department Chair Review',
                'assoc_dean_review' => 'Associate Dean Review',
                'dean_review' => 'Dean Review',
                'qa_review' => 'QA Quality Check',
            ];
        } elseif ($user->position === 'qa_representative') {
            return [
                'qa_review' => 'QA Quality Check',
            ];
        } elseif ($user->position === 'department_chair') {
            return [
                'pending_approval' => 'Awaiting Review',
                'dept_chair_review' => 'Department Chair Review',
            ];
        } elseif ($user->position === 'associate_dean') {
            return [
                'assoc_dean_review' => 'Associate Dean Review',
            ];
        } elseif ($user->position === 'dean') {
            return [
                'dean_review' => 'Dean Review',
            ];
        }

        return SyllabusConstants::getStatusOptions();
    }

    protected function getDefaultStatusFilter(): ?string
    {
        $user = auth()->user();

        if ($user->position === 'department_chair') {
            return 'pending_approval';
        } elseif ($user->position === 'associate_dean') {
            return 'assoc_dean_review';
        } elseif ($user->position === 'dean') {
            return 'dean_review';
        } elseif ($user->position === 'qa_representative') {
            return 'qa_review';
        }

        return null;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    protected function getDefaultTableRecordsPerPage(): int
    {
        return 10;
    }
}
