<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas;

use App\Constants\SyllabusConstants;
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
                Section::make('Approval Status & History')
                    ->description('Current approval status and workflow history')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Current Status')
                            ->badge()
                            ->color(fn ($state): string => is_string($state) ? SyllabusConstants::getStatusColor($state) : 'gray')
                            ->formatStateUsing(fn ($state): string => is_string($state) ? (SyllabusConstants::getStatusOptions()[$state] ?? str($state)->replace('_', ' ')->title()) : 'Unknown'),
                        
                        TextEntry::make('version')
                            ->label('Version')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted At')
                            ->dateTime()
                            ->placeholder('Not submitted'),

                        TextEntry::make('dept_chair_reviewed_at')
                            ->label('Dept. Chair Review')
                            ->dateTime()
                            ->placeholder('Pending'),

                        TextEntry::make('assoc_dean_reviewed_at')
                            ->label('Assoc. Dean Review')
                            ->dateTime()
                            ->placeholder('Pending'),

                        TextEntry::make('dean_approved_at')
                            ->label('Dean Approval')
                            ->dateTime()
                            ->placeholder('Pending'),

                        TextEntry::make('rejection_comments')
                            ->label('Rejection Comments')
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No rejection comments')
                            ->visible(fn ($record): bool => !empty($record->rejection_comments))
                            ->columnSpanFull(),

                        TextEntry::make('rejected_by_role')
                            ->label('Rejected By')
                            ->badge()
                            ->color(fn ($state): string => is_string($state) ? SyllabusConstants::getRoleColor($state) : 'gray')
                            ->formatStateUsing(fn ($state): string => is_string($state) ? (SyllabusConstants::APPROVAL_ROLES[$state] ?? str($state)->replace('_', ' ')->title()) : '')
                            ->visible(fn ($record): bool => !empty($record->rejected_by_role)),

                        TextEntry::make('rejected_at')
                            ->label('Rejected At')
                            ->dateTime()
                            ->visible(fn ($record): bool => !empty($record->rejected_at)),
                    ])
                    ->columns(3)
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
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Course Outcomes')
                    ->description('Define the learning outcomes for this course.')
                    ->schema([
                        RepeatableEntry::make('course_outcomes')
                            ->label('Learning Outcomes')
                            ->schema([
                                TextEntry::make('verb')
                                    ->label('Action Verb')
                                    ->badge()
                                    ->color(fn ($state): string => is_string($state) ? SyllabusConstants::getActionVerbColor($state) : 'gray')
                                    ->formatStateUsing(fn ($state): string => is_string($state) ? ucfirst($state) : ''),
                                
                                TextEntry::make('content')
                                    ->label('Outcome Description')
                                    ->html()
                                    ->formatStateUsing(function ($state): string {
                                        return is_string($state) ? $state : '';
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->placeholder('No course outcomes defined'),
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
                                TextEntry::make('week_range')
                                    ->label('Week(s)')
                                    ->formatStateUsing(function ($state): string {
                                        // Handle if state is not an array or is null
                                        if (!is_array($state) || empty($state)) {
                                            return 'N/A';
                                        }
                                        
                                        $start = $state['start'] ?? null;
                                        $end = $state['end'] ?? $start;
                                        // Handle is_range as both boolean and integer (1/0)
                                        $isRange = ($state['is_range'] ?? false) == true || ($state['is_range'] ?? 0) == 1;
                                        
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
                                
                                TextEntry::make('learning_outcomes')
                                    ->label('Learning Outcomes for this Week/Range')
                                    ->formatStateUsing(function ($state): string {
                                        if (!is_array($state) || empty($state)) {
                                            return 'No learning outcomes defined';
                                        }
                                        
                                        $outcomes = [];
                                        foreach ($state as $outcome) {
                                            if (isset($outcome['verb']) && isset($outcome['content'])) {
                                                $verb = is_string($outcome['verb']) ? ucfirst($outcome['verb']) : '';
                                                $content = is_string($outcome['content']) ? strip_tags($outcome['content']) : '';
                                                if ($verb && $content) {
                                                    $outcomes[] = "<strong>{$verb}:</strong> {$content}";
                                                }
                                            }
                                        }
                                        
                                        return empty($outcomes) ? 'No learning outcomes defined' : implode('<br>', $outcomes);
                                    })
                                    ->html()
                                    ->columnSpanFull(),
                                
                                TextEntry::make('learning_activities')
                                    ->label('Learning Activities')
                                    ->formatStateUsing(function ($state): string {
                                        if (!is_array($state) || empty($state)) {
                                            return 'No learning activities defined';
                                        }
                                        
                                        $activities = [];
                                        foreach ($state as $activity) {
                                            $activityParts = [];
                                            
                                            // Add modality
                                            if (isset($activity['modality']) && is_array($activity['modality'])) {
                                                $modalities = array_map(function ($modality) {
                                                    return is_string($modality) ? (SyllabusConstants::getLearningModalityOptions()[$modality] ?? ucfirst(str_replace('_', ' ', $modality))) : '';
                                                }, $activity['modality']);
                                                $modalities = array_filter($modalities); // Remove empty strings
                                                if (!empty($modalities)) {
                                                    $activityParts[] = '<strong>Modality:</strong> ' . implode(', ', $modalities);
                                                }
                                            }
                                            
                                            // Add reference
                                            if (isset($activity['reference']) && is_string($activity['reference']) && !empty($activity['reference'])) {
                                                $reference = strip_tags($activity['reference']);
                                                if (!empty($reference)) {
                                                    $activityParts[] = '<strong>Reference:</strong> ' . $reference;
                                                }
                                            }
                                            
                                            // Add description
                                            if (isset($activity['description']) && is_string($activity['description']) && !empty($activity['description'])) {
                                                $description = strip_tags($activity['description']);
                                                if (!empty($description)) {
                                                    $activityParts[] = '<strong>Description:</strong> ' . $description;
                                                }
                                            }
                                            
                                            if (!empty($activityParts)) {
                                                $activities[] = implode('<br>', $activityParts);
                                            }
                                        }
                                        
                                        return empty($activities) ? 'No learning activities defined' : implode('<hr style="margin: 10px 0;">', $activities);
                                    })
                                    ->html()
                                    ->columnSpanFull(),
                                
                                TextEntry::make('assessments')
                                    ->label('Weekly Assessments')
                                    ->html()
                                    ->formatStateUsing(function ($state): string {
                                        if (!is_array($state) || empty($state)) {
                                            return '<span class="text-gray-500">No assessments</span>';
                                        }
                                        
                                        $assessments = array_map(function ($assessment) {
                                            if (!is_string($assessment)) return '';
                                            $label = SyllabusConstants::getAssessmentTypeOptions()[$assessment] ?? ucfirst(str_replace('_', ' ', $assessment));
                                            return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-1 mb-1">' . htmlspecialchars($label) . '</span>';
                                        }, $state);
                                        
                                        $assessments = array_filter($assessments); // Remove empty strings
                                        return empty($assessments) ? '<span class="text-gray-500">No assessments</span>' : implode(' ', $assessments);
                                    }),
                            ])
                            ->columnSpanFull()
                            ->placeholder('No learning matrix defined'),
                    ])
                    ->columnSpanFull(),

                Section::make('References & Resources')
                    ->schema([
                        TextEntry::make('textbook_references')
                            ->label('Textbook References')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No textbook references')
                            ->columnSpanFull(),
                        
                        TextEntry::make('adaptive_digital_solutions')
                            ->label('Adaptive Digital Solutions')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No digital solutions')
                            ->columnSpanFull(),
                        
                        TextEntry::make('online_references')
                            ->label('Online References')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No online references')
                            ->columnSpanFull(),
                        
                        TextEntry::make('other_references')
                            ->label('Other References')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No other references')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Policies & Grading')
                    ->description('These fields are pre-filled with default content. You may modify them as needed.')
                    ->schema([
                        TextEntry::make('grading_system')
                            ->label('Grading System')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No grading system defined')
                            ->columnSpanFull(),
                        
                        TextEntry::make('classroom_policies')
                            ->label('Classroom Policies')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No classroom policies defined')
                            ->columnSpanFull(),
                        
                        TextEntry::make('consultation_hours')
                            ->label('Consultation Hours')
                            ->html()
                            ->formatStateUsing(function ($state): string {
                                return is_string($state) ? $state : '';
                            })
                            ->placeholder('No consultation hours defined')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Approval & Signers')
                    ->schema([
                        TextEntry::make('principalPreparer.full_name')
                            ->label('Principal Prepared By')
                            ->placeholder('Not assigned'),
                        
                        RepeatableEntry::make('prepared_by')
                            ->label('Additional Preparers')
                            ->schema([
                                TextEntry::make('user_id')
                                    ->label('Faculty Member')
                                    ->formatStateUsing(function ($state): string {
                                        if (!$state) return 'N/A';
                                        
                                        // Try to find user and return name safely
                                        try {
                                            $user = \App\Models\User::find($state);
                                            return $user?->full_name ?? $user?->name ?? 'Unknown User';
                                        } catch (\Exception $e) {
                                            return 'User ID: ' . $state;
                                        }
                                    }),
                                
                                TextEntry::make('role')
                                    ->label('Role/Position')
                                    ->formatStateUsing(function ($state): string {
                                        return is_string($state) ? $state : '';
                                    })
                                    ->placeholder('No role specified'),
                                
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->html()
                                    ->formatStateUsing(function ($state): string {
                                        return is_string($state) ? $state : '';
                                    })
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->placeholder('No additional preparers'),
                        
                        TextEntry::make('reviewer.full_name')
                            ->label('Reviewed By (Department Chair)')
                            ->placeholder('Not reviewed'),
                        
                        TextEntry::make('recommendingApprover.full_name')
                            ->label('Recommending Approval (Associate Dean)')
                            ->placeholder('Not recommended'),
                        
                        TextEntry::make('approver.full_name')
                            ->label('Approved By (Dean)')
                            ->placeholder('Not approved')
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
                            ->placeholder('0'),

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
