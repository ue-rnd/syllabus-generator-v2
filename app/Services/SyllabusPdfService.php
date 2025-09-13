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
    public function generatePdf(Syllabus $syllabus): \Barryvdh\DomPDF\PDF
    {
        // Load the syllabus with all relationships
        $syllabus->load([
            'course.college',
            'principalPreparer',
            'reviewer',
            'recommendingApprover',
            'approver'
        ]);

        // Prepare data for the PDF
        $data = $this->preparePdfData($syllabus);

        // Generate PDF
        $pdf = PDF::loadView('pdf.syllabus', $data);
        
        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ]);

        return $pdf;
    }

    /**
     * Prepare data for PDF generation
     */
    private function preparePdfData(Syllabus $syllabus): array
    {
        return [
            'syllabus' => $syllabus,
            'course' => $syllabus->course,
            'department' => null, // Not used in this architecture
            'college' => $syllabus->course->college ?? null,
            'preparers' => $this->getPreparersData($syllabus),
            'learning_matrix' => $this->processingLearningMatrix($syllabus->learning_matrix ?? []),
            'course_outcomes' => $syllabus->course_outcomes ?? [],
            'total_hours' => $syllabus->total_hours,
            'approval_details' => $syllabus->getApprovalStatusDetails(),
            'generated_at' => now(),
            'generated_by' => auth()->user(),
        ];
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
                    'content' => strip_tags($outcome['content'] ?? ''),
                    'full_text' => ucfirst($outcome['verb'] ?? '') . ' ' . strip_tags($outcome['content'] ?? ''),
                ];
            })->toArray();

            // Process learning activities
            $learningActivities = collect($item['learning_activities'] ?? [])->map(function ($activity) {
                return [
                    'modality' => is_array($activity['modality'] ?? []) 
                        ? implode(', ', $activity['modality']) 
                        : ($activity['modality'] ?? ''),
                    'reference' => strip_tags($activity['reference'] ?? ''),
                    'description' => strip_tags($activity['description'] ?? ''),
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
}
