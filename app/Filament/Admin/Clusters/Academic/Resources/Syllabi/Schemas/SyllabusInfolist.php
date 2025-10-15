<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas;

use App\Constants\SyllabusConstants;
use App\Models\Syllabus;
use App\Models\User;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SyllabusInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Approval Status & History')
                    ->description('Current approval status and workflow history')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Current Status')
                            ->badge()
                            ->color(function ($state) {
                                return SyllabusConstants::getStatusColor($state);
                            })
                            ->formatStateUsing(function ($state) {
                                return SyllabusConstants::getStatusOptions()[$state];
                            }),

                        TextEntry::make('version')
                            ->label('Version')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted At')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('dept_chair_reviewed_at')
                            ->label('Dept. Chair Review')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('assoc_dean_reviewed_at')
                            ->label('Assoc. Dean Review')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('dean_approved_at')
                            ->label('Dean Approval')
                            ->dateTime()
                            ->placeholder('-'),

                        // TextEntry::make('version')
                        //     ->label('Dean Approval')
                        //     ->formatStateUsing(function ($record) {
                        //         return $record;}),

                        RepeatableEntry::make('approval_history')
                            ->label('Approval History')
                            ->schema([
                                TextEntry::make('action')
                                    ->badge()
                                    ->formatStateUsing(function ($state) {
                                        return SyllabusConstants::getApprovalStatusOptions()[$state];
                                    })
                                    ->color(function ($state) {
                                        return SyllabusConstants::getApprovalStatusColor($state);
                                    }),

                                TextEntry::make('user_id')
                                    ->label('Faculty Member')
                                    ->formatStateUsing(function ($state): string {
                                        if (! $state) {
                                            return 'N/A';
                                        }

                                        try {
                                            $user = User::find($state);

                                            return $user->full_name ?? $user->name ?? 'Unknown User';
                                        } catch (\Exception $e) {
                                            return 'User ID: ' . $state;
                                        }
                                    }),
                                TextEntry::make('user_role')
                                    ->label('User Role')
                                    ->badge(),
                                TextEntry::make('comments')
                                    ->columnSpanFull(),
                                TextEntry::make('timestamp')
                                    ->label('Timestamp')
                                    ->dateTime(),
                            ])
                            ->placeholder('-')
                            ->columns(3)
                            ->columnSpanFull(),

                        TextEntry::make('rejection_comments')
                            ->label('Rejection Comments')
                            ->placeholder('-')
                            ->visible(fn ($record): bool => ! empty($record->rejection_comments))
                            ->columnSpanFull(),

                        TextEntry::make('rejected_by_role')
                            ->label('Rejected By')
                            ->badge()
                            ->color(fn ($state): string => SyllabusConstants::getRoleColor($state))
                            ->formatStateUsing(fn ($state): string => SyllabusConstants::getApprovalRoleOptions()[$state])
                            ->visible(fn ($record): bool => ! empty($record->rejected_by_role)),

                        TextEntry::make('rejected_at')
                            ->label('Rejected At')
                            ->dateTime()
                            ->visible(fn ($record): bool => ! empty($record->rejected_at)),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Curricular Details')
                    ->description('Academic year and schedule')
                    ->schema([
                        TextEntry::make('ay_start')
                            ->label('AY Start')
                            ->numeric()
                            ->columnSpan(3),
                        TextEntry::make('ay_end')
                            ->label('AY End')
                            ->numeric()
                            ->columnSpan(3),
                        TextEntry::make('week_prelim')
                            ->label('Prelims Exam Week')
                            ->numeric()
                            ->columnSpan(2),
                        TextEntry::make('week_midterm')
                            ->label('Midterms Exam Week')
                            ->numeric(),
                        TextEntry::make('week_final')
                            ->label('Finals Exam Week')
                            ->numeric(),
                    ])
                    ->columns(6)
                    ->columnSpanFull(),

                Section::make('Basic Information')
                    ->description('Course identification and college association')
                    ->schema([
                        TextEntry::make('course.name')
                            ->label('Course'),

                        TextEntry::make('name')
                            ->label('Syllabus Name'),

                        TextEntry::make('description')
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Program Outcomes')
                    ->description('Identify the program outcomes that this course addresses')
                    ->schema([
                        RepeatableEntry::make('program_outcomes')
                            ->label('Program Outcomes')
                            ->schema([
                                TextEntry::make('content')
                                    ->html()
                                    ->columnSpanFull(),

                                TextEntry::make('addressed')
                                    ->label('How It Was Addressed')
                                    ->formatStateUsing(function ($state): string {
                                        return SyllabusConstants::getOutcomesAddressedOptions()[$state];
                                    })
                                    ->badge()
                                    ->columnSpanFull()
                                    ->placeholder('-'),
                            ])
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Course Outcomes')
                    ->description('Define the learning outcomes for this course.')
                    ->schema([
                        RepeatableEntry::make('course_outcomes')
                            ->label('Learning Outcomes')
                            ->schema([
                                TextEntry::make('verb')
                                    ->visible(false),

                                TextEntry::make('content')
                                    ->visible(false),

                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->formatStateUsing(function ($get) {
                                        return SyllabusConstants::renderVerbAndContent($get('verb'), $get('content'));
                                    })
                                    ->html(),
                            ])
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),

                Section::make('Learning Matrix')
                    ->description('Define the learning activities, outcomes, and assessments for each week or week range.')
                    ->schema([
                        Section::make('Hours')
                            ->schema([
                                TextEntry::make('default_lecture_hours')
                                    ->label('Lecture Hours per Week')
                                    ->suffix(' hours'),

                                TextEntry::make('default_laboratory_hours')
                                    ->label('Laboratory Hours per Week')
                                    ->suffix(' hours'),
                            ])
                            ->columns(2),

                        RepeatableEntry::make('learning_matrix')
                            ->label('Learning Matrix Items')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('week_range.is_range')
                                            ->visible(false)
                                            ->live(),

                                        TextEntry::make('week_range.start')
                                            ->label(fn ($get) => $get('week_range.is_range') ? 'Week Start' : 'Week')
                                            ->numeric()
                                            ->placeholder('-'),

                                        TextEntry::make('week_range.end')
                                            ->label('Week End')
                                            ->numeric()
                                            ->visible(fn ($get) => $get('week_range.is_range'))
                                            ->placeholder('-'),
                                    ]),

                                TextEntry::make('content')
                                    ->label('Content')
                                    ->placeholder('-')
                                    ->html(),

                                RepeatableEntry::make('learning_outcomes')
                                    ->label('Learning Matrix Items')
                                    ->schema([
                                        TextEntry::make('verb')
                                            ->visible(false)
                                            ->live(),
                                        TextEntry::make('content')
                                            ->visible(false)
                                            ->live(),
                                        TextEntry::make('content')
                                            ->hiddenLabel()
                                            ->formatStateUsing(function ($get) {
                                                return SyllabusConstants::renderVerbAndContent($get('verb'), $get('content'));
                                            })
                                            ->html(),
                                    ]),

                                RepeatableEntry::make('learning_activities')
                                    ->label('Learning Activities')
                                    ->schema([
                                        TextEntry::make('description')
                                            ->placeholder('-')
                                            ->html(),
                                        TextEntry::make('modality')
                                            ->formatStateUsing(function ($state) {
                                                return SyllabusConstants::getLearningModalityOptions()[$state];
                                            })
                                            ->color(function ($state) {
                                                return SyllabusConstants::getLearningModalityColor($state);
                                            })
                                            ->badge()
                                            ->live(),
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->html(),
                                    ]),

                                TextEntry::make('assessments')
                                    ->label('Weekly Assessments')
                                    ->html()
                                    ->placeholder('-'),
                            ])
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),

                Section::make('References & Resources')
                    ->schema([
                        TextEntry::make('textbook_references')
                            ->label('Textbook References')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('adaptive_digital_solutions')
                            ->label('Adaptive Digital Solutions')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('online_references')
                            ->label('Online References')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('other_references')
                            ->label('Other References')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Policies & Grading')
                    ->description('These fields are pre-filled with default content. You may modify them as needed.')
                    ->schema([
                        TextEntry::make('grading_system')
                            ->label('Grading System')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('classroom_policies')
                            ->label('Classroom Policies')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('consultation_hours')
                            ->label('Consultation Hours')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Approval & Signers')
                    ->schema([
                        TextEntry::make('principalPreparer.full_name')
                            ->label('Principal Prepared By')
                            ->placeholder('-'),

                        RepeatableEntry::make('prepared_by')
                            ->label('Additional Preparers')
                            ->schema([
                                TextEntry::make('user_id')
                                    ->label('Faculty Member')
                                    ->formatStateUsing(function ($state): string {
                                        if (! $state) {
                                            return 'N/A';
                                        }

                                        try {
                                            $user = User::find($state);

                                            return $user->full_name ?? $user->name ?? 'Unknown User';
                                        } catch (\Exception $e) {
                                            return 'User ID: ' . $state;
                                        }
                                    }),

                                TextEntry::make('role')
                                    ->label('Role/Position')
                                    ->formatStateUsing(function ($state): string {
                                        return is_string($state) ? $state : '';
                                    })
                                    ->placeholder('-'),

                                TextEntry::make('description')
                                    ->label('Description')
                                    ->html()
                                    ->hidden()
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->placeholder('-'),

                        TextEntry::make('reviewer.full_name')
                            ->label('Reviewed By (Department Chair)')
                            ->placeholder('-'),

                        TextEntry::make('recommendingApprover.full_name')
                            ->label('Recommending Approval (Associate Dean)')
                            ->placeholder('-'),

                        TextEntry::make('approver.full_name')
                            ->label('Approved By (Dean)')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->placeholder('-'),

                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime()
                            ->visible(fn (Syllabus $record): bool => $record->trashed())
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
