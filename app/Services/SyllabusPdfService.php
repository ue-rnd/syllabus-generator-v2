<?php

namespace App\Services;

use App\Models\Syllabus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class SyllabusPdfService
{
    /**
     * Generate PDF for a syllabus
     */
    public function generatePdf(Syllabus $syllabus, bool $useNewTemplate = true): \Barryvdh\DomPDF\PDF
    {
        try {
            // Load the syllabus with all relationships
            $syllabus->load([
                'course.college.departments',
                'principalPreparer',
                'reviewer',
                'recommendingApprover',
                'approver'
            ]);

            // Prepare data for the PDF
            $data = $this->preparePdfData($syllabus);

            // Choose template based on flag
            $template = 'pdf.syllabus';

            // Generate PDF
            $pdf = PDF::loadView($template, $data);
            
            // Configure PDF settings
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

            return $pdf;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'syllabus_id' => $syllabus->id,
                'course_id' => $syllabus->course_id,
                'trace' => $e->getTraceAsString()
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
            // Basic data with error handling
            $course = $syllabus->course;
            $college = $course->college ?? null;
            
            if (!$course) {
                $errors[] = 'Course information is missing';
            }
            
            if (!$college) {
                $errors[] = 'College information is missing';
            }

            // Prepare prerequisites
            $prerequisites = [];
            if ($course && !empty($course->prerequisite_courses)) {
                try {
                    // prerequisite_courses is cast as array in the Course model
                    $prerequisiteIds = $course->prerequisite_courses;
                    
                    if (!empty($prerequisiteIds) && is_array($prerequisiteIds)) {
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
                    $errors[] = 'Error loading prerequisites: ' . $e->getMessage();
                }
            }

            // Prepare program outcomes (template for now)
            $programOutcomes = [];
            
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
                'syllabus' => $syllabus,
                'course' => $course,
                'college' => $college,
                'prerequisites' => $prerequisites,
                'preparers' => $preparers,
                'approvers' => $approvers,
                'references' => $references,
                'otherElements' => $otherElements,
                'programOutcomes' => $programOutcomes,
                'learning_matrix' => $this->processingLearningMatrix($syllabus->learning_matrix ?? []),
                'course_outcomes' => $syllabus->course_outcomes ?? [],
                'total_hours' => $syllabus->total_hours,
                'approval_details' => $syllabus->getApprovalStatusDetails(),
                'academicYear' => $this->getCurrentAcademicYear(),
                'generated_at' => now(),
                'generated_by' => auth()->user(),
                'logo_path' => public_path('images/logo_ue.png'),
                'logo_url' => asset('images/logo_ue.png'),
                'logo_base64' => $this->getLogoBase64(),
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            $errors[] = 'Data preparation error: ' . $e->getMessage();
            
            // Return minimal data structure to prevent template errors
            return [
                'syllabus' => $syllabus,
                'course' => $syllabus->course ?? new \App\Models\Course(),
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
                'academicYear' => date('Y') . '-' . (date('Y') + 1),
                'generated_at' => now(),
                'generated_by' => auth()->user(),
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
        if (!empty($syllabus->prepared_by)) {
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
    private function processingLearningMatrix(array $learningMatrix): array
    {
        return collect($learningMatrix)->map(function ($item) {
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
                    'full_text' => ucfirst($outcome['verb'] ?? '') . ' ' . ($outcome['content'] ?? ''),
                ];
            })->toArray();

            // Process learning activities
            $learningActivities = collect($item['learning_activities'] ?? [])->map(function ($activity) {
                return [
                    'modality' => is_array($activity['modality'] ?? []) 
                        ? implode(', ', $activity['modality']) 
                        : ($activity['modality'] ?? ''),
                    'reference' => $activity['reference'] ?? '',
                    'description' => $activity['description'] ?? '',
                ];
            })->toArray();

            // Process assessments
            $assessments = is_array($item['assessments'] ?? []) 
                ? implode(', ', $item['assessments']) 
                : ($item['assessments'] ?? '');

            return [
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
        $pdf = $this->generatePdf($syllabus);
        
        if (!$filename) {
            $courseCode = $syllabus->course->code ?? 'COURSE';
            $courseName = str_replace(' ', '_', $syllabus->course->name ?? 'Syllabus');
            $version = $syllabus->version ?? 1;
            $filename = "{$courseCode}_{$courseName}_Syllabus_v{$version}.pdf";
        }

        return $pdf->download($filename);
    }

    /**
     * Stream PDF for inline viewing
     */
    public function streamPdf(Syllabus $syllabus): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generatePdf($syllabus);
        
        $courseCode = $syllabus->course->code ?? 'COURSE';
        $filename = "{$courseCode}_Syllabus.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Generate error PDF when main generation fails
     */
    private function generateErrorPdf(Syllabus $syllabus, string $errorMessage): \Barryvdh\DomPDF\PDF
    {
        $errorData = [
            'syllabus' => $syllabus,
            'course' => $syllabus->course ?? null,
            'errorMessage' => $errorMessage,
            'generated_at' => now(),
        ];

        $html = view('pdf.error', $errorData)->render();
        
        return PDF::loadHTML($html)->setPaper('A4', 'portrait');
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
            return $currentYear . '-' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '-' . $currentYear;
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
            return 'data:image/png;base64,' . $imageData;
        }
        
        return null;
    }
}
