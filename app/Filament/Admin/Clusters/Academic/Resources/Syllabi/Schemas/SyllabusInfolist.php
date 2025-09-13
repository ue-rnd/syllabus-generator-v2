<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas;

use App\Models\Syllabus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SyllabusInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('course.name')
                            ->label('Course'),
                        TextEntry::make('created_at')
                            ->label('Date Created')
                            ->dateTime()
                            ->tooltip('The creation date determines which version is active (latest = active)'),
                        IconEntry::make('is_latest')
                            ->label('Latest Version')
                            ->boolean()
                            ->getStateUsing(fn (Syllabus $record): bool => $record->isLatest())
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('approval_status')
                            ->label('Approval Status'),
                        TextEntry::make('description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Hours Configuration')
                    ->schema([
                        TextEntry::make('default_lecture_hours')
                            ->label('Default Lecture Hours per Week')
                            ->suffix(' hours'),
                        TextEntry::make('default_laboratory_hours')
                            ->label('Default Laboratory Hours per Week')
                            ->suffix(' hours'),
                        TextEntry::make('total_hours.total')
                            ->label('Total Semester Hours')
                            ->suffix(' hours')
                            ->tooltip(fn (Syllabus $record) => 
                                "Lecture: {$record->total_hours['lecture']} hrs, Lab: {$record->total_hours['laboratory']} hrs, Weeks: {$record->total_hours['weeks']}"
                            ),
                    ])
                    ->columns(3),

                Section::make('Course Outcomes')
                    ->schema([
                        RepeatableEntry::make('course_outcomes')
                            ->label('Learning Outcomes')
                            ->schema([
                                TextEntry::make('verb')
                                    ->label('Action Verb')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('content')
                                    ->label('Outcome Description')
                                    ->html()
                                    ->getStateUsing(function (array $state): string {
                                        return is_string($state['content'] ?? '') ? $state['content'] : '';
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->placeholder('No course outcomes defined'),
                    ]),

                Section::make('Weekly Learning Matrix')
                    ->schema([
                        RepeatableEntry::make('learning_matrix')
                            ->label('Weekly Learning Items')
                            ->schema([
                                TextEntry::make('week_display')
                                    ->label('Week(s)')
                                    ->getStateUsing(function (array $state): string {
                                        if (!isset($state['week_range'])) {
                                            return 'N/A';
                                        }
                                        
                                        $weekRange = $state['week_range'];
                                        $start = $weekRange['start'] ?? null;
                                        $end = $weekRange['end'] ?? $start;
                                        $isRange = $weekRange['is_range'] ?? false;
                                        
                                        if (!$start) {
                                            return 'N/A';
                                        }
                                        
                                        if ($isRange && $end && $end != $start) {
                                            return "Weeks {$start}-{$end}";
                                        }
                                        
                                        return "Week {$start}";
                                    })
                                    ->badge()
                                    ->color('info'),
                                
                                RepeatableEntry::make('learning_outcomes')
                                    ->label('Learning Outcomes')
                                    ->schema([
                                        TextEntry::make('outcome_display')
                                            ->label('')
                                            ->getStateUsing(function (array $state): string {
                                                $verb = ucfirst($state['verb'] ?? '');
                                                $content = is_string($state['content'] ?? '') ? strip_tags($state['content']) : '';
                                                return $verb && $content ? "{$verb} {$content}" : 'N/A';
                                            })
                                            ->columnSpanFull(),
                                    ]),
                                
                                RepeatableEntry::make('learning_activities')
                                    ->label('Learning Activities')
                                    ->schema([
                                        TextEntry::make('modality')
                                            ->label('Modality')
                                            ->badge(),
                                        TextEntry::make('reference')
                                            ->label('Reference')
                                            ->html()
                                            ->getStateUsing(function (array $state): string {
                                                return is_string($state['reference'] ?? '') ? $state['reference'] : '';
                                            })
                                            ->columnSpanFull(),
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->html()
                                            ->getStateUsing(function (array $state): string {
                                                return is_string($state['description'] ?? '') ? $state['description'] : '';
                                            })
                                            ->columnSpanFull(),
                                    ]),
                                
                                TextEntry::make('assessments')
                                    ->label('Assessments')
                                    ->listWithLineBreaks()
                                    ->placeholder('No assessments'),
                            ])
                            ->columnSpanFull()
                            ->placeholder('No learning matrix defined'),
                    ]),

                Section::make('References & Resources')
                    ->schema([
                        TextEntry::make('textbook_references')
                            ->label('Textbook References')
                            ->html()
                            ->placeholder('No textbook references')
                            ->columnSpanFull(),
                        
                        TextEntry::make('adaptive_digital_solutions')
                            ->label('Adaptive Digital Solutions')
                            ->html()
                            ->placeholder('No digital solutions')
                            ->columnSpanFull(),
                        
                        TextEntry::make('online_references')
                            ->label('Online References')
                            ->html()
                            ->placeholder('No online references')
                            ->columnSpanFull(),
                        
                        TextEntry::make('other_references')
                            ->label('Other References')
                            ->html()
                            ->placeholder('No other references')
                            ->columnSpanFull(),
                    ]),

                Section::make('Policies & Grading')
                    ->schema([
                        TextEntry::make('grading_system')
                            ->label('Grading System')
                            ->html()
                            ->placeholder('No grading system defined')
                            ->columnSpanFull(),
                        
                        TextEntry::make('classroom_policies')
                            ->label('Classroom Policies')
                            ->html()
                            ->placeholder('No classroom policies defined')
                            ->columnSpanFull(),
                        
                        TextEntry::make('consultation_hours')
                            ->label('Consultation Hours')
                            ->html()
                            ->placeholder('No consultation hours defined')
                            ->columnSpanFull(),
                    ]),

                Section::make('Approval & Signers')
                    ->schema([
                        TextEntry::make('principalPreparer.name')
                            ->label('Principal Prepared By'),
                        
                        RepeatableEntry::make('preparers')
                            ->label('Additional Preparers')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name'),
                                TextEntry::make('role')
                                    ->label('Role'),
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->html()
                                    ->getStateUsing(function (array $state): string {
                                        return is_string($state['description'] ?? '') ? $state['description'] : '';
                                    }),
                            ])
                            ->columnSpanFull(),
                        
                        TextEntry::make('reviewer.name')
                            ->label('Reviewed By (Dept. Chair)')
                            ->placeholder('Not reviewed'),
                        
                        TextEntry::make('recommendingApprover.name')
                            ->label('Recommending Approval (Assoc. Dean)')
                            ->placeholder('Not recommended'),
                        
                        TextEntry::make('approver.name')
                            ->label('Approved By (Dean)')
                            ->placeholder('Not approved'),
                    ])
                    ->columns(2),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                        
                        TextEntry::make('deleted_at')
                            ->label('Deleted')
                            ->dateTime()
                            ->visible(fn (Syllabus $record): bool => $record->trashed()),
                    ])
                    ->columns(3),
            ]);
    }
}
