<?php

namespace App\Livewire\Client\Syllabi;

use App\Models\Course;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
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
            }
        } else {
            $this->course = null;
            $this->program_outcomes = [];
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
            // Add other step validations as we implement them
        }
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
            'assessmentTypes' => $assessmentTypes
        ]);
    }
}
