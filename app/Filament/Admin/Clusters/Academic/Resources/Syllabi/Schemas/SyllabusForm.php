<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Schemas;

use App\Constants\SyllabusConstants;
use App\Models\Course;
use App\Models\User;
use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SyllabusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Curricular Details')
                    ->description('Academic year and schedule')
                    ->schema([
                        TextInput::make('ay_start')
                            ->label('AY Start')
                            ->numeric()
                            ->default(function () {
                                $setting = Setting::where('key', 'default_ay_start')->first();
                                return $setting ? $setting->value : null;
                            })
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('ay_end')
                            ->label('AY End')
                            ->numeric()
                            ->default(function () {
                                $setting = Setting::where('key', 'default_ay_end')->first();
                                return $setting ? $setting->value : null;
                            })
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('week_prelim')
                            ->label('Prelims Week')
                            ->numeric()
                            ->default(function () {
                                $setting = Setting::where('key', 'default_week_prelim')->first();
                                return $setting ? $setting->value : null;
                            })
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('week_midterm')
                            ->label('Midterms Week')
                            ->numeric()
                            ->default(function () {
                                $setting = Setting::where('key', 'default_week_midterm')->first();
                                return $setting ? $setting->value : null;
                            })
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('week_final')
                            ->label('Finals Week')
                            ->numeric()
                            ->default(function () {
                                $setting = Setting::where('key', 'default_week_final')->first();
                                return $setting ? $setting->value : null;
                            })
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(6)
                    ->columnSpanFull(),
                Section::make('Basic Information')
                    ->description('Course identification and college association')
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if ($state) {
                                    $course = Course::find($state);

                                    if ($course) {
                                        $set('name', $course->name . ' Syllabus ' . '(' . (new \DateTime())->format('Y-m-d') . ')');
                                        $set('recommending_approval', $course->college->associate_dean_id);
                                        $set('approved_by', $course->college->dean_id);

                                        $program = $course->programs()->first();
                                        $programOutcomes = $program->outcomes;

                                        function parseOutcome(string $outcomes) {
                                            $outcome = [];

                                            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $outcomes, $matches);

                                            foreach ($matches[1] as $li) {
                                                $outcome[] = array(
                                                    'content' => trim($li),
                                                    'addressed' => []
                                                );
                                            }
                                            return $outcome;
                                        }

                                        $programOutcomesFormatted = parseOutcome($programOutcomes);

                                        $set('program_outcomes', $programOutcomesFormatted);
                                    }
                                }
                            })
                            ,

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                            ->options(SyllabusConstants::getStatusOptions())
                            ->default('draft')
                            ->required()
                            ->searchable()
                            ->columnSpanFull()
                            ->disabled(),

                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Program Outcomes')
                    ->description('Identify the program outcomes that this course fulfills')
                    ->schema([
                        Repeater::make('program_outcomes')
                            ->label('Program Outcomes')
                            ->schema([
                                TextInput::make('content')->readOnly(),

                                // RichEditor::make('content')
                                //     ->label('Content')
                                //     ->placeholder('Content...')
                                //     ->required()
                                //     ->toolbarButtons([])
                                //     ->columnSpanFull(),

                                Select::make('addressed')
                                    ->label('How It Was Addressed')
                                    ->options(SyllabusConstants::getOutcomesAddressedOptions())
                                    ->multiple()
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select how the outcome was addressed')
                                    ->columnSpanFull(),
                            ])
                            ->addable(false),
                    ])
                    ->columnSpanFull(),

                Section::make('Course Outcomes')
                    ->description('Define the learning outcomes for this course.')
                    ->schema([
                        Repeater::make('course_outcomes')
                            ->label('Learning Outcomes')
                            ->schema([
                                Select::make('verb')
                                    ->label('Action Verb')
                                    ->options(SyllabusConstants::getActionVerbOptions())
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select an action verb'),

                                RichEditor::make('content')
                                    ->label('Outcome Description')
                                    ->placeholder('Complete the outcome statement...')
                                    ->required()
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add Course Outcome')
                            ->collapsible()
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Learning Matrix')
                    ->description('Define the learning activities, outcomes, and assessments for each week or week range.')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('default_lecture_hours')
                                    ->label('Default Lecture Hours per Week')
                                    ->numeric()
                                    ->step(0.5)
                                    ->default(3.0)
                                    ->required()
                                    ->suffix('hours'),

                                TextInput::make('default_laboratory_hours')
                                    ->label('Default Laboratory Hours per Week')
                                    ->numeric()
                                    ->step(0.5)
                                    ->default(0.0)
                                    ->required()
                                    ->suffix('hours'),
                            ])
                            ->columns(2),
                        Repeater::make('learning_matrix')
                            ->label('Items')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('week_range.is_range')
                                            ->label('Week Range')
                                            ->helperText('Toggle to specify a range of weeks instead of a single week')
                                            ->live()
                                            ->default(false)
                                            ->columnSpanFull(),

                                        TextInput::make('week_range.start')
                                            ->label(fn($get) => $get('week_range.is_range') ? 'Week Start' : 'Week')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(20),

                                        TextInput::make('week_range.end')
                                            ->label('Week End')
                                            ->numeric()
                                            ->required(fn($get) => $get('week_range.is_range'))
                                            ->visible(fn($get) => $get('week_range.is_range'))
                                            ->minValue(fn($get) => $get('week_range.start') ?? 1)
                                            ->maxValue(20),
                                    ]),

                                RichEditor::make('content')
                                    ->label('Content')
                                    ->placeholder('Add item content...')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),

                                Repeater::make('learning_outcomes')
                                    ->label('Learning Outcomes for this Week/Range')
                                    ->schema([
                                        Select::make('verb')
                                            ->label('Action Verb')
                                            ->options(SyllabusConstants::getActionVerbOptions())
                                            ->searchable()
                                            ->required()
                                            ->placeholder('Select an action verb'),

                                        RichEditor::make('content')
                                            ->label('Outcome Description')
                                            ->placeholder('Complete the outcome statement...')
                                            ->required()
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add Learning Outcome')
                                    ->collapsible()
                                    ->hiddenLabel()
                                    ->columnSpanFull(),

                                Repeater::make('learning_activities')
                                    ->label('Learning Activities')
                                    ->schema([
                                        RichEditor::make('description')
                                            ->label('Activity Description')
                                            ->required()
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),

                                        Select::make('modality')
                                            ->multiple()
                                            ->options(SyllabusConstants::getLearningModalityOptions())
                                            ->required(),

                                        RichEditor::make('reference')
                                            ->label('Reference/Resource')
                                            ->placeholder('Add reference or resource details...')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add Learning Activity')
                                    ->collapsible()
                                    ->columnSpanFull(),

                                Select::make('assessments')
                                    ->label('Weekly Assessments')
                                    ->options(SyllabusConstants::getAssessmentTypeOptions())
                                    ->multiple()
                                    ->searchable()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (!isset($state['week_range'])) {
                                    return 'New Week';
                                }

                                $weekRange = $state['week_range'];
                                $start = $weekRange['start'] ?? null;
                                $end = $weekRange['end'] ?? null;
                                $isRange = $weekRange['is_range'] ?? false;

                                if (!$start) {
                                    return 'New Week';
                                }

                                if ($isRange && $end && $end != $start) {
                                    return "Weeks {$start}-{$end}";
                                }

                                return "Week {$start}";
                            })
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (empty($value)) {
                                            return;
                                        }

                                        $occupiedWeeks = [];
                                        $errors = [];

                                        foreach ($value as $index => $item) {
                                            if (empty($item['week_range'])) {
                                                continue;
                                            }

                                            $weekRange = $item['week_range'];

                                            $start = $weekRange['start'] ?? null;
                                            $end = $weekRange['end'] ?? $start;
                                            $isRange = $weekRange['is_range'] ?? false;

                                            // Ensure $start and $end are numeric
                                            if (!is_null($start) && !is_numeric($start)) {
                                                $start = preg_replace('/[^\d.]/', '', (string)$start);
                                            }
                                            if (!is_null($end) && !is_numeric($end)) {
                                                $end = preg_replace('/[^\d.]/', '', (string)$end);
                                            }
                                            $start = is_numeric($start) ? (int)$start : null;
                                            $end = is_numeric($end) ? (int)$end : $start;

                                            if (!$start) {
                                                $errors[] = "Item " . ($index + 1) . ": Week is required";
                                                continue;
                                            }

                                            if ($isRange && $start > $end) {
                                                $errors[] = "Item " . ($index + 1) . ": Start week must be less than or equal to end week";
                                                continue;
                                            }

                                            // Check for overlaps
                                            for ($week = $start; $week <= $end; $week++) {
                                                if (isset($occupiedWeeks[$week])) {
                                                    $errors[] = "Week {$week} is used in multiple items (Item " . ($index + 1) . ")";
                                                } else {
                                                    $occupiedWeeks[$week] = true;
                                                }
                                            }
                                        }

                                        if (!empty($errors)) {
                                            $fail('Week validation errors: ' . implode('; ', $errors));
                                        }
                                    };
                                },
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('References & Resources')
                    ->schema([
                        RichEditor::make('textbook_references')
                            ->label('Textbook References')
                            ->placeholder('Add textbook references...')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('adaptive_digital_solutions')
                            ->label('Adaptive Digital Solutions')
                            ->placeholder('Add digital solutions...')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('online_references')
                            ->label('Online References')
                            ->placeholder('Add online references...')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('other_references')
                            ->label('Other References')
                            ->placeholder('Add other references...')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Policies & Grading')
                    ->description('These fields are pre-filled with default content. You may modify them as needed.')
                    ->schema([
                        RichEditor::make('grading_system')
                            ->label('Grading System')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ])
                            ->default('<table><tr><th>Component</th><th>Percentage</th></tr><tr><td>Class Participation</td><td>10%</td></tr><tr><td>Quizzes & Assignments</td><td>30%</td></tr><tr><td>Midterm Examination</td><td>25%</td></tr><tr><td>Final Examination</td><td>35%</td></tr></table><br><strong>Grading Scale:</strong><br>A: 90-100<br>B: 80-89<br>C: 70-79<br>D: 60-69<br>F: Below 60')
                            ->columnSpanFull(),

                        RichEditor::make('classroom_policies')
                            ->label('Classroom Policies')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->default("1. Attendance is mandatory for all class sessions.<br>2. Late submissions will be penalized according to the course policy.<br>3. Academic integrity must be maintained at all times.<br>4. Respectful behavior is expected from all students.<br>5. Electronic devices should be used for academic purposes only during class.")
                            ->columnSpanFull(),

                        RichEditor::make('consultation_hours')
                            ->label('Consultation Hours')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->default("Monday to Friday: 2:00 PM - 4:00 PM<br>By appointment: Contact through official email<br>Response time: Within 24-48 hours for email inquiries")
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Approval & Signers')
                    ->schema([
                        Select::make('principal_prepared_by')
                            ->label('Principal Prepared By')
                            ->relationship('principalPreparer', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name ?? $record->name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload()
                            ->required()
                            ->default(auth()->id())
                            ->columnSpanFull(),

                        Repeater::make('prepared_by')
                            ->label('Additional Preparers')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Faculty Member')
                                    ->options(User::all()->pluck('full_name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->preload(),

                                TextInput::make('role')
                                    ->label('Role/Position')
                                    ->placeholder('e.g., Faculty, Distinguished Faculty, Library Officer')
                                    ->maxLength(255),

                                RichEditor::make('description')
                                    ->label('Description')
                                    ->placeholder('Additional details about their contribution')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add Preparer')
                            ->collapsible()
                            ->columnSpanFull(),

                        Select::make('reviewed_by')
                            ->label('Reviewed By (Department Chair)')
                            ->relationship('reviewer', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name ?? $record->name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->default(function ($get) {

                            })
                            ->preload()
                            ->columnSpanFull(),

                        Select::make('recommending_approval')
                            ->label('Recommending Approval (Associate Dean)')
                            ->relationship('recommendingApprover', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name ?? $record->name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload(),

                        Select::make('approved_by')
                            ->label('Approved By (Dean)')
                            ->relationship('approver', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name ?? $record->name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload(),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
