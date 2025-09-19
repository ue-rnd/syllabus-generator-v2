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
    public $validatedSteps = [1]; // Track which steps have been validated
    
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
    
    public function mount()
    {
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

    public function addPreparer(): void
    {
        $this->prepared_by[] = [
            'user_id' => null,
            'role' => '',
            'description' => '',
        ];
    }

    public function removePreparer(int $index): void
    {
        if (isset($this->prepared_by[$index])) {
            unset($this->prepared_by[$index]);
            $this->prepared_by = array_values($this->prepared_by);
        }
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
                // Auto-fill description from course description (preserve HTML formatting but clean whitespace)
                $rawDescription = $this->course->description ?? '';
                // Remove leading/trailing whitespace and normalize excessive whitespace while preserving HTML structure
                $this->description = preg_replace('/\s+/', ' ', trim($rawDescription));

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

                $this->reviewed_by = $department?->department_chair_id ?? null;
                $this->recommending_approval = $college?->associate_dean_id ?? null;
                $this->approved_by = $college?->dean_id ?? null;
            }
        } else {
            $this->course = null;
            $this->program_outcomes = [];
            $this->description = '';
            $this->reviewed_by = null;
            $this->recommending_approval = null;
            $this->approved_by = null;
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

    private function normalizePreparedBy(): void
    {
        if (!is_array($this->prepared_by)) {
            return;
        }

        foreach ($this->prepared_by as $index => $item) {
            if (!is_array($item)) {
                $this->prepared_by[$index] = [];
                continue;
            }

            $rawValue = $item['user_id'] ?? null;

            // Handle common shapes: scalar string/number, arrays, or objects
            if (is_array($rawValue)) {
                if (array_key_exists('value', $rawValue)) {
                    $rawValue = $rawValue['value'];
                } elseif (array_key_exists('id', $rawValue)) {
                    $rawValue = $rawValue['id'];
                } elseif (array_key_exists(0, $rawValue)) {
                    $rawValue = $rawValue[0];
                }
            } elseif (is_object($rawValue)) {
                if (isset($rawValue->value)) {
                    $rawValue = $rawValue->value;
                } elseif (isset($rawValue->id)) {
                    $rawValue = $rawValue->id;
                }
            }

            // Cast numeric strings to int
            if (is_string($rawValue) && ctype_digit($rawValue)) {
                $rawValue = (int) $rawValue;
            }

            // Ensure final value is either int or null
            if (!is_null($rawValue) && !is_int($rawValue)) {
                $rawValue = null;
            }

            $this->prepared_by[$index]['user_id'] = $rawValue;
        }
    }
    
    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;
        
        // Mark the current step as validated
        if (!in_array($this->currentStep, $this->validatedSteps)) {
            $this->validatedSteps[] = $this->currentStep;
        }
        
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
            // Allow going to previous steps or current step
            if ($step <= $this->currentStep || in_array($step, $this->validatedSteps)) {
                $this->currentStep = $step;
                $this->dispatch('step-changed');
            } else {
                // Prevent jumping to unvalidated future steps
                session()->flash('error', 'Please complete the current step before proceeding to step ' . $step . '.');
            }
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
                    'program_outcomes.*.addressed.*' => 'required|string|in:introduced,enhanced,demonstrated',
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
                    'learning_matrix.*.week_range.start.max' => 'This field cannot be greater than 20',
                    'learning_matrix.*.week_range.end.max' => 'This field cannot be greater than 20.'
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
            case 7:
                $this->normalizePreparedBy();
                $this->validate([
                    'principal_prepared_by' => 'required|integer|exists:users,id',
                    'reviewed_by' => 'nullable|integer|exists:users,id',
                    'recommending_approval' => 'nullable|integer|exists:users,id',
                    'approved_by' => 'nullable|integer|exists:users,id',
                    'prepared_by' => 'nullable|array',
                    'prepared_by.*.user_id' => 'required_with:prepared_by|exists:users,id|different:principal_prepared_by',
                    'prepared_by.*.role' => 'nullable|string|max:255',
                    'prepared_by.*.description' => 'nullable|string',
                ], [
                    'prepared_by.*.user_id.required_with' => 'Please select a faculty member.',
                    'prepared_by.*.user_id.different' => 'Co-editor cannot be the principal preparer.',
                ]);
                break;
            // Add other step validations as we implement them
        }
    }

    public function submit()
    {
        $this->normalizePreparedBy();
        // Validate all steps before saving
        // For drafts, validate only minimal required fields to allow incomplete content
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'principal_prepared_by' => 'required|integer|exists:users,id',
            // everything else optional for draft
            'prepared_by' => 'nullable|array',
            'prepared_by.*.user_id' => 'nullable|exists:users,id|different:principal_prepared_by',
            'prepared_by.*.role' => 'nullable|string|max:255',
            'prepared_by.*.description' => 'nullable|string',
        ], [
            'program_outcomes.required' => 'Program outcomes are required. Please select a course first.',
            'program_outcomes.min' => 'At least one program outcome must be available.',
            'program_outcomes.*.addressed.required' => 'Please select how this program outcome is addressed.',
            'program_outcomes.*.addressed.min' => 'Please select at least one addressing method for this program outcome.',
            'program_outcomes.*.addressed.*.required' => 'Please select a valid addressing method.',
            'program_outcomes.*.addressed.*.in' => 'Please select a valid addressing method.',
            'principal_prepared_by.required' => 'Principal preparer is required.',
            'prepared_by.*.user_id.different' => 'Co-editor cannot be the principal preparer.',
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

        // Sanitize optional approvers to null if invalid/empty
        $reviewedBy = $this->reviewed_by && User::find($this->reviewed_by) ? (int) $this->reviewed_by : null;
        $recommendingApproval = $this->recommending_approval && User::find($this->recommending_approval) ? (int) $this->recommending_approval : null;
        $approvedBy = $this->approved_by && User::find($this->approved_by) ? (int) $this->approved_by : null;

        // Create and save as Draft
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
            'reviewed_by' => $reviewedBy,
            'recommending_approval' => $recommendingApproval,
            'approved_by' => $approvedBy,
            'status' => 'draft',
            'submitted_at' => null,
            'week_prelim' => $this->week_prelim,
            'week_midterm' => $this->week_midterm,
            'week_final' => $this->week_final,
            'ay_start' => $this->ay_start,
            'ay_end' => $this->ay_end,
            'program_outcomes' => $this->program_outcomes,
        ]);

        session()->flash('success', 'Syllabus saved as draft.');
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
            'currentStep' => $this->currentStep,
            'totalSteps' => $this->totalSteps,
            // Approver display names
            'principalPreparerName' => $this->principal_prepared_by ? (User::find($this->principal_prepared_by)?->full_name ?? User::find($this->principal_prepared_by)?->name ?? null) : null,
            'reviewerName' => $this->reviewed_by ? (User::find($this->reviewed_by)?->full_name ?? User::find($this->reviewed_by)?->name ?? null) : null,
            'recommendingName' => $this->recommending_approval ? (User::find($this->recommending_approval)?->full_name ?? User::find($this->recommending_approval)?->name ?? null) : null,
            'approverName' => $this->approved_by ? (User::find($this->approved_by)?->full_name ?? User::find($this->approved_by)?->name ?? null) : null,
            'facultyOptions' => User::all()->mapWithKeys(fn($u) => [$u->id => ($u->full_name ?? $u->name)])->toArray(),
        ]); 
    }
}
