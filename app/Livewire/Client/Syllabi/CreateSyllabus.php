<?php

namespace App\Livewire\Client\Syllabi;

use App\Models\Course;
use App\Models\User;
use App\Models\Syllabus;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('livewire.client.dashboard.base')]
class CreateSyllabus extends Component
{
    public $currentStep = 1;
    public $totalSteps = 7;
    
    // Step 1: Basic Information and Curricular Details
    #[Validate('required|numeric')]
    public $ay_start;
    
    #[Validate('required|numeric')]
    public $ay_end;
    
    #[Validate('required|numeric|min:1|max:20')]
    public $week_prelim;
    
    #[Validate('required|numeric|min:1|max:20')]
    public $week_midterm;
    
    #[Validate('required|numeric|min:1|max:20')]
    public $week_final;
    
    #[Validate('required|exists:courses,id')]
    public $course_id;
    
    #[Validate('required|string|max:255')]
    public $name;
    
    #[Validate('nullable|string')]
    public $description;
    
    public $course = null;
    public $program_outcomes = [];
    public $course_outcomes = [];
    
    // Learning Matrix properties
    public $default_lecture_hours = 3.0;
    public $default_laboratory_hours = 0.0;
    public $learning_matrix = [];
    
    // References & Resources properties
    public $textbook_references = '';
    public $adaptive_digital_solutions = '';
    public $online_references = '';
    public $other_references = '';
    
    // Policies & Grading properties
    public $grading_system = '<table><tr><th>Component</th><th>Percentage</th></tr><tr><td>Class Participation</td><td>10%</td></tr><tr><td>Quizzes & Assignments</td><td>30%</td></tr><tr><td>Midterm Examination</td><td>25%</td></tr><tr><td>Final Examination</td><td>35%</td></tr></table><br><strong>Grading Scale:</strong><br>A: 90-100<br>B: 80-89<br>C: 70-79<br>D: 60-69<br>F: Below 60';
    public $classroom_policies = '1. Attendance is mandatory for all class sessions.<br>2. Late submissions will be penalized according to the course policy.<br>3. Academic integrity must be maintained at all times.<br>4. Respectful behavior is expected from all students.<br>5. Electronic devices should be used for academic purposes only during class.';
    public $consultation_hours = 'Monday to Friday: 2:00 PM - 4:00 PM<br>By appointment: Contact through official email<br>Response time: Within 24-48 hours for email inquiries';
    
    // Approval & Signers properties
    public $principal_prepared_by = '';
    public $prepared_by = [];
    public $reviewed_by = '';
    public $recommending_approval = '';
    public $approved_by = '';
    public $showConfirmModal = false;
    
    public ?Syllabus $existing = null;

    public function mount(?Syllabus $syllabus = null)
    {
        // If editing, hydrate from existing syllabus
        if ($syllabus) {
            $this->existing = $syllabus->load(['course.programs.department', 'course.college']);

            $this->ay_start = $syllabus->ay_start;
            $this->ay_end = $syllabus->ay_end;
            $this->week_prelim = $syllabus->week_prelim;
            $this->week_midterm = $syllabus->week_midterm;
            $this->week_final = $syllabus->week_final;
            $this->course_id = $syllabus->course_id;
            $this->name = $syllabus->name;
            $this->description = $syllabus->description;
            $this->course = $syllabus->course;
            $this->program_outcomes = $syllabus->program_outcomes ?? [];
            $this->course_outcomes = $syllabus->course_outcomes ?? [];
            $this->default_lecture_hours = (float) $syllabus->default_lecture_hours;
            $this->default_laboratory_hours = (float) $syllabus->default_laboratory_hours;
            $this->learning_matrix = $syllabus->learning_matrix ?? [];
            $this->textbook_references = $syllabus->textbook_references ?? '';
            $this->adaptive_digital_solutions = $syllabus->adaptive_digital_solutions ?? '';
            $this->online_references = $syllabus->online_references ?? '';
            $this->other_references = $syllabus->other_references ?? '';
            $this->grading_system = $syllabus->grading_system ?? $this->grading_system;
            $this->classroom_policies = $syllabus->classroom_policies ?? $this->classroom_policies;
            $this->consultation_hours = $syllabus->consultation_hours ?? $this->consultation_hours;
            $this->principal_prepared_by = $syllabus->principal_prepared_by;
            $this->prepared_by = $syllabus->prepared_by ?? [];
            $this->reviewed_by = $syllabus->reviewed_by;
            $this->recommending_approval = $syllabus->recommending_approval;
            $this->approved_by = $syllabus->approved_by;

            return; // Skip defaults below
        }

        // Set default values from settings
        $this->ay_start = Setting::where('key', 'default_ay_start')->first()?->value ?? date('Y');
        $this->ay_end = Setting::where('key', 'default_ay_end')->first()?->value ?? (date('Y') + 1);
        $this->week_prelim = Setting::where('key', 'default_week_prelim')->first()?->value ?? 6;
        $this->week_midterm = Setting::where('key', 'default_week_midterm')->first()?->value ?? 12;
        $this->week_final = Setting::where('key', 'default_week_final')->first()?->value ?? 18;
        
        // Initialize course as null
        $this->course = null;
        $this->program_outcomes = [];
        $this->course_outcomes = [];
        $this->learning_matrix = [];
        $this->prepared_by = [];

        // Set principal preparer as the authenticated user by default
        $this->principal_prepared_by = Auth::id();
    }

    public function confirmSubmit(): void
    {
        $this->showConfirmModal = true;
    }
    
    public function updatedCourseId($value)
    {
        if ($value) {
            $this->course = Course::with(['programs.department', 'college'])->find($value);
            
            if ($this->course) {                
                // Get program outcomes
                $program = $this->course->programs()->first();
                if ($program && $program->outcomes) {
                    try {
                        $this->program_outcomes = $this->parseProgramOutcomes($program->outcomes);
                    } catch (\Exception $e) {
                        // If parsing fails, set empty array
                        $this->program_outcomes = [];
                        Log::warning('Failed to parse program outcomes: ' . $e->getMessage());
                    }
                } else {
                    $this->program_outcomes = [];
                }

                // Auto-populate approvers based on course relationships
                $department = $program?->department;
                $college = $this->course->college;

                $this->reviewed_by = $department?->department_chair_id ?? '';
                $this->recommending_approval = $college?->associate_dean_id ?? '';
                $this->approved_by = $college?->dean_id ?? '';
            }
        } else {
            $this->course = null;
            $this->program_outcomes = [];
            $this->reviewed_by = '';
            $this->recommending_approval = '';
            $this->approved_by = '';
        }
    }
    
    public function addCourseOutcome()
    {
        $this->course_outcomes[] = [
            'verb' => '',
            'content' => ''
        ];
    }
    
    public function removeCourseOutcome($index)
    {
        unset($this->course_outcomes[$index]);
        $this->course_outcomes = array_values($this->course_outcomes);
    }
    
    // Learning Matrix methods
    public function addLearningMatrixItem()
    {
        $this->learning_matrix[] = [
            'week_range' => [
                'is_range' => false,
                'start' => null,
                'end' => null
            ],
            'content' => '',
            'learning_outcomes' => [],
            'learning_activities' => [],
            'assessments' => []
        ];
    }
    
    public function removeLearningMatrixItem($index)
    {
        unset($this->learning_matrix[$index]);
        $this->learning_matrix = array_values($this->learning_matrix);
    }
    
    public function addLearningOutcome($matrixIndex)
    {
        $this->learning_matrix[$matrixIndex]['learning_outcomes'][] = [
            'verb' => '',
            'content' => ''
        ];
    }
    
    public function removeLearningOutcome($matrixIndex, $outcomeIndex)
    {
        unset($this->learning_matrix[$matrixIndex]['learning_outcomes'][$outcomeIndex]);
        $this->learning_matrix[$matrixIndex]['learning_outcomes'] = array_values($this->learning_matrix[$matrixIndex]['learning_outcomes']);
    }
    
    public function addLearningActivity($matrixIndex)
    {
        $this->learning_matrix[$matrixIndex]['learning_activities'][] = [
            'description' => '',
            'modality' => [],
            'reference' => ''
        ];
    }
    
    public function removeLearningActivity($matrixIndex, $activityIndex)
    {
        unset($this->learning_matrix[$matrixIndex]['learning_activities'][$activityIndex]);
        $this->learning_matrix[$matrixIndex]['learning_activities'] = array_values($this->learning_matrix[$matrixIndex]['learning_activities']);
    }
    
    private function authorizeEdit(): void
    {
        $userId = Auth::id();
        if (!($this->existing && (
            $this->existing->principal_prepared_by === $userId ||
            collect($this->existing->prepared_by)->contains('user_id', $userId)
        ))) {
            abort(403, 'You are not authorized to edit this syllabus.');
        }
    }
    
    private function parseProgramOutcomes($outcomes)
    {
        $parsedOutcomes = [];
        
        // Check if outcomes is already an array or a string
        if (is_array($outcomes)) {
            // If it's already an array, process each item
            foreach ($outcomes as $outcome) {
                if (is_string($outcome)) {
                    $parsedOutcomes[] = [
                        'content' => trim(strip_tags($outcome)),
                        'addressed' => []
                    ];
                } elseif (is_array($outcome) && isset($outcome['content'])) {
                    // If it's already in the expected format, use it as is
                    $parsedOutcomes[] = [
                        'content' => trim(strip_tags($outcome['content'])),
                        'addressed' => $outcome['addressed'] ?? []
                    ];
                }
            }
        } elseif (is_string($outcomes)) {
            // If it's a string, parse the HTML list items
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $outcomes, $matches);
            
            foreach ($matches[1] as $li) {
                $parsedOutcomes[] = [
                    'content' => trim(strip_tags($li)),
                    'addressed' => []
                ];
            }
        }
        
        return $parsedOutcomes;
    }
    
    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;
        $this->dispatch('step-changed');
    }
    
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('step-changed');
        }
    }
    
    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->dispatch('step-changed');
        }
    }
    
    private function validateStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'ay_start' => 'required|numeric',
                    'ay_end' => 'required|numeric',
                    'week_prelim' => 'required|numeric|min:1|max:20',
                    'week_midterm' => 'required|numeric|min:1|max:20',
                    'week_final' => 'required|numeric|min:1|max:20',
                    'course_id' => 'required|exists:courses,id',
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ]);
                break;
            case 2:
                $this->validate([
                    'program_outcomes' => 'required|array|min:1',
                    'program_outcomes.*.addressed' => 'required|array|min:1',
                    'program_outcomes.*.addressed.*' => 'required|string|in:Introduced,Enhanced,Demonstrated',
                ], [
                    'program_outcomes.required' => 'Program outcomes are required. Please select a course first.',
                    'program_outcomes.min' => 'At least one program outcome must be available.',
                    'program_outcomes.*.addressed.required' => 'Please select how this program outcome is addressed.',
                    'program_outcomes.*.addressed.min' => 'Please select at least one addressing method for this program outcome.',
                    'program_outcomes.*.addressed.*.required' => 'Please select a valid addressing method.',
                    'program_outcomes.*.addressed.*.in' => 'Please select a valid addressing method.',
                ]);
                break;
            case 3:
                $this->validate([
                    'course_outcomes' => 'required|array|min:1',
                    'course_outcomes.*.verb' => 'required|string',
                    'course_outcomes.*.content' => 'required|string|min:10',
                ], [
                    'course_outcomes.required' => 'At least one course outcome is required.',
                    'course_outcomes.min' => 'At least one course outcome must be added.',
                    'course_outcomes.*.verb.required' => 'Please select an action verb for this outcome.',
                    'course_outcomes.*.content.required' => 'Please provide a description for this outcome.',
                    'course_outcomes.*.content.min' => 'The outcome description must be at least 10 characters long.',
                ]);
                break;
            case 4:
                $this->validate([
                    'default_lecture_hours' => 'nullable|numeric|min:0',
                    'default_laboratory_hours' => 'nullable|numeric|min:0',
                    'learning_matrix' => 'required|array|min:1',
                    'learning_matrix.*.week_range.is_range' => 'nullable|boolean',
                    'learning_matrix.*.week_range.start' => 'required|integer|min:1|max:20',
                    'learning_matrix.*.week_range.end' => 'nullable|integer|min:1|max:20',
                    'learning_matrix.*.content' => 'required|string|min:3',
                    // learning_outcomes, learning_activities, assessments are optional
                ], [
                    'learning_matrix.required' => 'Please add at least one learning matrix item.',
                    'learning_matrix.min' => 'Please add at least one learning matrix item.',
                    'learning_matrix.*.week_range.start.required' => 'Please specify the week (start).',
                    'learning_matrix.*.content.required' => 'Content is required for each matrix item.',
                ]);

                // Additional validation: if is_range is true, end must be >= start
                foreach ($this->learning_matrix as $idx => $item) {
                    $isRange = $item['week_range']['is_range'] ?? false;
                    $start = $item['week_range']['start'] ?? null;
                    $end = $item['week_range']['end'] ?? null;
                    if ($isRange) {
                        if ($end === null) {
                            $this->addError("learning_matrix.$idx.week_range.end", 'Week end is required when using a range.');
                        } elseif ($start !== null && $end < $start) {
                            $this->addError("learning_matrix.$idx.week_range.end", 'Week end must be greater than or equal to week start.');
                        }
                    }
                }
                break;
            case 5:
                $this->validate([
                    'textbook_references' => 'nullable|string',
                    'adaptive_digital_solutions' => 'nullable|string',
                    'online_references' => 'nullable|string',
                    'other_references' => 'nullable|string',
                ]);
                break;
            case 6:
                $this->validate([
                    'grading_system' => 'required|string|min:10',
                    'classroom_policies' => 'required|string|min:10',
                    'consultation_hours' => 'nullable|string',
                ], [
                    'grading_system.required' => 'Please define the grading system.',
                    'classroom_policies.required' => 'Please define classroom policies.',
                ]);
                break;
            // Add other step validations as we implement them
        }
    }

    public function submit()
    {
        if ($this->existing) {
            $this->authorizeEdit();

            $this->validate([ // minimal validation for update
                'name' => 'required|string|max:255',
            ]);

            $this->existing->update([
                'name' => $this->name,
                'description' => $this->description,
                'course_id' => $this->course_id,
                'default_lecture_hours' => $this->default_lecture_hours,
                'default_laboratory_hours' => $this->default_laboratory_hours,
                'course_outcomes' => $this->course_outcomes,
                'learning_matrix' => $this->learning_matrix,
                'textbook_references' => $this->textbook_references,
                'adaptive_digital_solutions' => $this->adaptive_digital_solutions,
                'online_references' => $this->online_references,
                'other_references' => $this->other_references,
                'grading_system' => $this->grading_system,
                'classroom_policies' => $this->classroom_policies,
                'consultation_hours' => $this->consultation_hours,
                'principal_prepared_by' => $this->principal_prepared_by,
                'prepared_by' => $this->prepared_by,
                'reviewed_by' => $this->reviewed_by,
                'recommending_approval' => $this->recommending_approval,
                'approved_by' => $this->approved_by,
                'week_prelim' => $this->week_prelim,
                'week_midterm' => $this->week_midterm,
                'week_final' => $this->week_final,
                'ay_start' => $this->ay_start,
                'ay_end' => $this->ay_end,
                'program_outcomes' => $this->program_outcomes,
            ]);

            session()->flash('success', 'Syllabus updated successfully.');
            return redirect()->route('home');
        }

        // Validate all steps before saving
        $this->validate([
            // Step 1
            'ay_start' => 'required|numeric',
            'ay_end' => 'required|numeric',
            'week_prelim' => 'required|numeric|min:1|max:20',
            'week_midterm' => 'required|numeric|min:1|max:20',
            'week_final' => 'required|numeric|min:1|max:20',
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Step 2
            'program_outcomes' => 'required|array|min:1',
            'program_outcomes.*.addressed' => 'required|array|min:1',
            'program_outcomes.*.addressed.*' => 'required|string|in:Introduced,Enhanced,Demonstrated',
            // Step 3
            'course_outcomes' => 'required|array|min:1',
            'course_outcomes.*.verb' => 'required|string',
            'course_outcomes.*.content' => 'required|string|min:10',
            // Step 4
            'default_lecture_hours' => 'nullable|numeric|min:0',
            'default_laboratory_hours' => 'nullable|numeric|min:0',
            'learning_matrix' => 'required|array|min:1',
            'learning_matrix.*.week_range.is_range' => 'nullable|boolean',
            'learning_matrix.*.week_range.start' => 'required|integer|min:1|max:20',
            'learning_matrix.*.week_range.end' => 'nullable|integer|min:1|max:20',
            'learning_matrix.*.content' => 'required|string|min:3',
            // Step 5
            'textbook_references' => 'nullable|string',
            'adaptive_digital_solutions' => 'nullable|string',
            'online_references' => 'nullable|string',
            'other_references' => 'nullable|string',
            // Step 6
            'grading_system' => 'required|string|min:10',
            'classroom_policies' => 'required|string|min:10',
            'consultation_hours' => 'nullable|string',
            // Step 7
            'principal_prepared_by' => 'required|integer|exists:users,id',
            'reviewed_by' => 'required|integer|exists:users,id',
            'recommending_approval' => 'required|integer|exists:users,id',
            'approved_by' => 'required|integer|exists:users,id',
        ], [
            'program_outcomes.required' => 'Program outcomes are required. Please select a course first.',
            'program_outcomes.min' => 'At least one program outcome must be available.',
            'program_outcomes.*.addressed.required' => 'Please select how this program outcome is addressed.',
            'program_outcomes.*.addressed.min' => 'Please select at least one addressing method for this program outcome.',
            'program_outcomes.*.addressed.*.required' => 'Please select a valid addressing method.',
            'program_outcomes.*.addressed.*.in' => 'Please select a valid addressing method.',
            'course_outcomes.required' => 'At least one course outcome is required.',
            'course_outcomes.min' => 'At least one course outcome must be added.',
            'course_outcomes.*.verb.required' => 'Please select an action verb for this outcome.',
            'course_outcomes.*.content.required' => 'Please provide a description for this outcome.',
            'course_outcomes.*.content.min' => 'The outcome description must be at least 10 characters long.',
            'learning_matrix.required' => 'Please add at least one learning matrix item.',
            'learning_matrix.min' => 'Please add at least one learning matrix item.',
            'learning_matrix.*.week_range.start.required' => 'Please specify the week (start).',
            'learning_matrix.*.content.required' => 'Content is required for each matrix item.',
            'grading_system.required' => 'Please define the grading system.',
            'classroom_policies.required' => 'Please define classroom policies.',
            'principal_prepared_by.required' => 'Principal preparer is required.',
            'reviewed_by.required' => 'Department chair is required.',
            'recommending_approval.required' => 'Associate dean is required.',
            'approved_by.required' => 'Dean is required.',
        ]);

        // Additional range validation for step 4
        foreach ($this->learning_matrix as $idx => $item) {
            $isRange = $item['week_range']['is_range'] ?? false;
            $start = $item['week_range']['start'] ?? null;
            $end = $item['week_range']['end'] ?? null;
            if ($isRange) {
                if ($end === null) {
                    $this->addError("learning_matrix.$idx.week_range.end", 'Week end is required when using a range.');
                } elseif ($start !== null && $end < $start) {
                    $this->addError("learning_matrix.$idx.week_range.end", 'Week end must be greater than or equal to week start.');
                }
            }
        }

        // Create and submit Syllabus for approval
        $syllabus = Syllabus::create([
            'name' => $this->name,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'default_lecture_hours' => $this->default_lecture_hours,
            'default_laboratory_hours' => $this->default_laboratory_hours,
            'course_outcomes' => $this->course_outcomes,
            'learning_matrix' => $this->learning_matrix,
            'textbook_references' => $this->textbook_references,
            'adaptive_digital_solutions' => $this->adaptive_digital_solutions,
            'online_references' => $this->online_references,
            'other_references' => $this->other_references,
            'grading_system' => $this->grading_system,
            'classroom_policies' => $this->classroom_policies,
            'consultation_hours' => $this->consultation_hours,
            'principal_prepared_by' => $this->principal_prepared_by,
            'prepared_by' => $this->prepared_by,
            'reviewed_by' => $this->reviewed_by,
            'recommending_approval' => $this->recommending_approval,
            'approved_by' => $this->approved_by,
            'status' => 'pending_approval',
            'submitted_at' => now(),
            'week_prelim' => $this->week_prelim,
            'week_midterm' => $this->week_midterm,
            'week_final' => $this->week_final,
            'ay_start' => $this->ay_start,
            'ay_end' => $this->ay_end,
            'program_outcomes' => $this->program_outcomes,
        ]);

        session()->flash('success', 'Syllabus created and submitted for approval.');
        return redirect()->route('home');
    }
    
    public function render()
    {
        $courses = Course::with(['programs', 'college'])->get();
        $actionVerbs = \App\Constants\SyllabusConstants::getActionVerbOptions();
        $learningModalities = \App\Constants\SyllabusConstants::getLearningModalityOptions();
        $assessmentTypes = \App\Constants\SyllabusConstants::getAssessmentTypeOptions();
        
        return view('livewire.client.syllabi.create-syllabus', [
            'courses' => $courses,
            'actionVerbs' => $actionVerbs,
            'learningModalities' => $learningModalities,
            'assessmentTypes' => $assessmentTypes,
            // Approver display names
            'principalPreparerName' => $this->principal_prepared_by ? (User::find($this->principal_prepared_by)?->full_name ?? User::find($this->principal_prepared_by)?->name ?? null) : null,
            'reviewerName' => $this->reviewed_by ? (User::find($this->reviewed_by)?->full_name ?? User::find($this->reviewed_by)?->name ?? null) : null,
            'recommendingName' => $this->recommending_approval ? (User::find($this->recommending_approval)?->full_name ?? User::find($this->recommending_approval)?->name ?? null) : null,
            'approverName' => $this->approved_by ? (User::find($this->approved_by)?->full_name ?? User::find($this->approved_by)?->name ?? null) : null,
        ]);
    }
}
