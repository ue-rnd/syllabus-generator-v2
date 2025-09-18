<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class SyllabusPdfService
{
    /**
     * Generate PDF for a syllabus using Spatie Browsershot
     */
    public function generatePdf(Syllabus $syllabus, bool $useNewTemplate = true): string
    {
        try {
            // Load the syllabus with all relationships
            $syllabus->load([
                'course.college.departments',
                'principalPreparer',
                'reviewer',
                'recommendingApprover',
                'approver',
            ]);

            // Prepare data for the PDF
            $data = $this->preparePdfData($syllabus);

            // Choose template based on flag
            $template = 'pdf.syllabus';

            // Create temporary directory for PDF generation
            $temporaryDirectory = (new TemporaryDirectory)->create();
            $pdfPath = $temporaryDirectory->path('syllabus.pdf');

            // Use Spatie LaravelPdf for PDF generation with footerView
            $pdf = \Spatie\LaravelPdf\Facades\Pdf::view($template, $data)
                ->footerView('pdf.syllabus-footer', $data)
                ->landscape();

            // Optionally set format and margins if needed (using config)
            $config = config('browsershot');
            if (isset($config['pdf']['format'])) {
                $pdf->format($config['pdf']['format']);
            }
            if (isset($config['pdf']['margins'])) {
                $m = $config['pdf']['margins'];
                $pdf->margins($m['top'], $m['right'], $m['bottom'], $m['left']);
            }

            $pdf->save($pdfPath);

            return $pdfPath;
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('PDF Generation Error: '.$e->getMessage(), [
                'syllabus_id' => $syllabus->id,
                'course_id' => $syllabus->course_id,
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error PDF with basic information
            return $this->generateErrorPdf($syllabus, $e->getMessage());
        }
    }

    /**
     * Prepare data for PDF generation
     */
    private function preparePdfData(Syllabus $syllabus): array
    {
        $errors = [];

        try {
            $university_mission = Setting::where('key', 'university_mission')->value('value');
            $university_vision = Setting::where('key', 'university_vision')->value('value');
            $university_core_values = Setting::where('key', 'university_core_values')->value('value');
            $university_guiding_principles = Setting::where('key', 'university_guiding_principles')->value('value');
            $university_institutional_outcomes = Setting::where('key', 'university_institutional_outcomes')->value('value');

            // Basic data with error handling
            $course = $syllabus->course;
            $college = $course->college ?? null;

            if (! $course) {
                $errors[] = 'Course information is missing';
            }

            if (! $college) {
                $errors[] = 'College information is missing';
            }

            // Prepare prerequisites
            $prerequisites = [];
            if ($course && ! empty($course->prerequisite_courses)) {
                try {
                    // prerequisite_courses is cast as array in the Course model
                    $prerequisiteIds = $course->prerequisite_courses;

                    if (! empty($prerequisiteIds) && is_array($prerequisiteIds)) {
                        $prerequisites = \App\Models\Course::whereIn('id', $prerequisiteIds)
                            ->get(['id', 'code', 'name'])
                            ->map(function ($prereq) {
                                return [
                                    'courseCode' => $prereq->code,
                                    'courseTitle' => $prereq->name,
                                    'code' => $prereq->code,
                                    'name' => $prereq->name,
                                ];
                            })->toArray();
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Error loading prerequisites: '.$e->getMessage();
                }
            }

            $program = $syllabus->course->programs()->first();

            $programObjectives = $program->objectives;

            $programOutcomes = $syllabus->program_outcomes ?? [];

            // Prepare preparers data
            $preparers = $this->getPreparersData($syllabus);

            // Prepare approvers data
            $approvers = [
                'departmentChair' => $syllabus->reviewer->full_name ?? '',
                'associateDean' => $syllabus->recommendingApprover->full_name ?? '',
                'dean' => $syllabus->approver->full_name ?? '',
            ];

            // Prepare references data
            $references = [
                'adaptiveDigitalSolutions' => $syllabus->adaptive_digital_solutions ?? 'Not specified',
                'textbook' => $syllabus->textbook_references ?? 'Not specified',
                'onlineReferences' => $syllabus->online_references ?? 'Not specified',
                'otherReferences' => $syllabus->other_references ?? 'Not specified',
            ];

            // Prepare other elements data
            $otherElements = [
                'gradingSystem' => $syllabus->grading_system ?? 'Standard University grading system applies.',
                'classroomPolicies' => $syllabus->classroom_policies ?? 'Standard classroom policies apply.',
                'consultationHours' => $syllabus->consultation_hours ?? 'By appointment.',
            ];

            return [
                'university_mission' => $university_mission,
                'university_vision' => $university_vision,
                'university_core_values' => $university_core_values,
                'university_guiding_principles' => $university_guiding_principles,
                'university_institutional_outcomes' => $university_institutional_outcomes,

                'syllabus' => $syllabus,
                'course' => $course,
                'college' => $college,
                'prerequisites' => $prerequisites,
                'preparers' => $preparers,
                'approvers' => $approvers,
                'references' => $references,
                'otherElements' => $otherElements,
                'programObjectives' => $programObjectives,
                'programOutcomes' => $programOutcomes,
                'learning_matrix' => $this->processingLearningMatrix($syllabus, $syllabus->learning_matrix ?? []),
                'course_outcomes' => $syllabus->course_outcomes ?? [],
                'total_hours' => $syllabus->total_hours,
                'approval_details' => $syllabus->getApprovalStatusDetails(),
                'academicYear' => $this->getCurrentAcademicYear(),
                'generated_at' => now(),
                'generated_by' => Auth::user(),
                'logo_path' => public_path('images/logo_ue.png'),
                'logo_url' => asset('images/logo_ue.png'),
                'logo_base64' => $this->getLogoBase64(),
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            $errors[] = 'Data preparation error: '.$e->getMessage();

            // Return minimal data structure to prevent template errors
            return [
                'university_mission' => $university_mission,
                'university_vision' => $university_vision,
                'university_core_values' => $university_core_values,
                'university_guiding_principles' => $university_guiding_principles,
                'university_institutional_outcomes' => $university_institutional_outcomes,

                'syllabus' => $syllabus,
                'course' => $syllabus->course ?? new \App\Models\Course,
                'college' => null,
                'prerequisites' => [],
                'preparers' => [],
                'approvers' => [],
                'references' => [],
                'otherElements' => [],
                'programOutcomes' => [],
                'learning_matrix' => [],
                'course_outcomes' => [],
                'total_hours' => ['lecture' => 0, 'laboratory' => 0, 'total' => 0, 'weeks' => 0],
                'approval_details' => ['status' => 'error'],
                'academicYear' => date('Y').'-'.(date('Y') + 1),
                'generated_at' => now(),
                'generated_by' => Auth::user(),
                'logo_path' => public_path('images/logo_ue.png'),
                'logo_url' => asset('images/logo_ue.png'),
                'logo_base64' => $this->getLogoBase64(),
                'errors' => $errors,
            ];
        }
    }

    /**
     * Get preparers data with user information
     */
    private function getPreparersData(Syllabus $syllabus): array
    {
        $preparers = [];

        // Principal preparer
        if ($syllabus->principalPreparer) {
            $preparers[] = [
                'name' => $syllabus->principalPreparer->full_name,
                'role' => 'Principal Preparer',
                'position' => $syllabus->principalPreparer->position ?? '',
                'description' => 'Principal Preparer',
                'is_principal' => true,
            ];
        }

        // Additional preparers
        if (! empty($syllabus->prepared_by)) {
            foreach ($syllabus->prepared_by as $preparer) {
                if (isset($preparer['user_id'])) {
                    $user = \App\Models\User::find($preparer['user_id']);
                    if ($user) {
                        $preparers[] = [
                            'name' => $user->full_name,
                            'role' => $preparer['role'] ?? 'Faculty',
                            'position' => $user->position ?? '',
                            'description' => $preparer['description'] ?? '',
                            'is_principal' => false,
                        ];
                    }
                }
            }
        }

        return $preparers;
    }

    /**
     * Process learning matrix for PDF display
     */
    private function processingLearningMatrix(Syllabus $syllabus, array $learningMatrix): array
    {
        return collect($learningMatrix)->map(function ($item) use ($syllabus) {
            // Process week range
            $weekDisplay = 'N/A';
            if (isset($item['week_range'])) {
                $weekRange = $item['week_range'];
                $start = $weekRange['start'] ?? null;
                $end = $weekRange['end'] ?? $start;
                $isRange = $weekRange['is_range'] ?? false;

                if ($start) {
                    if ($isRange && $end && $end != $start) {
                        $weekDisplay = "Weeks {$start}-{$end}";
                    } else {
                        $weekDisplay = "Week {$start}";
                    }
                }
            }

            // Process learning outcomes
            $learningOutcomes = collect($item['learning_outcomes'] ?? [])->map(function ($outcome) {
                return [
                    'verb' => ucfirst($outcome['verb'] ?? ''),
                    'content' => $outcome['content'] ?? '',
                    'full_text' => ucfirst($outcome['verb'] ?? '').' '.($outcome['content'] ?? ''),
                ];
            })->toArray();

            // Process learning activities
            $learningActivities = collect($item['learning_activities'] ?? [])->map(function ($activity) {
                return [
                    'modality' => $activity['modality'] ?? '',
                    'reference' => $activity['reference'] ?? '',
                    'description' => $activity['description'] ?? '',
                ];
            })->toArray();

            // Process assessments
            $assessments = $item['assessments'] ?? [];

            return [
                'week_prelim' => $syllabus->week_prelim,
                'week_midterm' => $syllabus->week_midterm,
                'week_final' => $syllabus->week_final,
                'start' => $start,
                'end' => $end,
                'week_display' => $weekDisplay,
                'learning_outcomes' => $learningOutcomes,
                'learning_activities' => $learningActivities,
                'assessments' => $assessments,
            ];
        })->sortBy(function ($item) {
            // Sort by week number for proper ordering
            preg_match('/(\d+)/', $item['week_display'], $matches);

            return isset($matches[1]) ? (int) $matches[1] : 999;
        })->values()->toArray();
    }

    /**
     * Download PDF with proper filename
     */
    public function downloadPdf(Syllabus $syllabus, ?string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdfPath = $this->generatePdf($syllabus);

        if (! $filename) {
            $courseCode = $syllabus->course->code ?? 'COURSE';
            $courseName = str_replace(' ', '_', $syllabus->course->name ?? 'Syllabus');
            $version = $syllabus->version ?? 1;
            $filename = "{$courseCode}_{$courseName}_Syllabus_v{$version}.pdf";
        }

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Stream PDF for inline viewing
     */
    public function streamPdf(Syllabus $syllabus): \Symfony\Component\HttpFoundation\Response
    {
        $pdfPath = $this->generatePdf($syllabus);

        $courseCode = $syllabus->course->code ?? 'COURSE';
        $filename = "{$courseCode}_Syllabus.pdf";

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Generate error PDF when main generation fails
     */
    private function generateErrorPdf(Syllabus $syllabus, string $errorMessage): string
    {
        $errorData = [
            'syllabus' => $syllabus,
            'course' => $syllabus->course ?? null,
            'errorMessage' => $errorMessage,
            'generated_at' => now(),
        ];

        $html = view('pdf.error', $errorData)->render();

        // Create temporary directory for error PDF
        $temporaryDirectory = (new TemporaryDirectory)->create();
        $pdfPath = $temporaryDirectory->path('error.pdf');

        // Generate simple error PDF
        $config = config('browsershot');

        Browsershot::html($html)
            ->format($config['pdf']['format'])
            ->portrait()
            ->margins(
                $config['pdf']['margins']['top'],
                $config['pdf']['margins']['right'],
                $config['pdf']['margins']['bottom'],
                $config['pdf']['margins']['left']
            )
            ->showBackground()
            ->emulateMedia('print')
            ->timeout($config['timeout'] / 2) // Shorter timeout for error PDF
            ->setOption('args', $config['chrome_args'])
            ->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear(): string
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        // Academic year typically starts in August/September
        if ($currentMonth >= 8) {
            return $currentYear.'-'.($currentYear + 1);
        } else {
            return ($currentYear - 1).'-'.$currentYear;
        }
    }

    /**
     * Get base64 encoded logo for PDF embedding
     */
    private function getLogoBase64(): ?string
    {
        $logoPath = public_path('images/logo_ue.png');

        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));

            return 'data:image/png;base64,'.$imageData;
        }

        return null;
    }
}
