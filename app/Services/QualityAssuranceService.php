<?php

namespace App\Services;

use App\Models\QualityChecklist;
use App\Models\Syllabus;
use App\Models\SyllabusQualityCheck;
use Illuminate\Support\Collection;

class QualityAssuranceService
{
    public function runAutomatedQualityCheck(Syllabus $syllabus, ?QualityChecklist $checklist = null): SyllabusQualityCheck
    {
        // Use default checklist if none provided
        if (!$checklist) {
            $checklist = $this->getDefaultChecklistForSyllabus($syllabus);
        }

        // Create or get existing quality check
        $qualityCheck = SyllabusQualityCheck::firstOrCreate([
            'syllabus_id' => $syllabus->id,
            'quality_checklist_id' => $checklist->id,
        ], [
            'status' => 'in_progress',
            'auto_generated' => true,
        ]);

        // Run the quality check
        $qualityCheck->runQualityCheck();

        return $qualityCheck;
    }

    public function runBulkQualityCheck(Collection $syllabi, ?QualityChecklist $checklist = null): Collection
    {
        $results = collect();

        foreach ($syllabi as $syllabus) {
            try {
                $result = $this->runAutomatedQualityCheck($syllabus, $checklist);
                $results->push($result);
            } catch (\Exception $e) {
                \Log::error("Failed to run quality check for syllabus {$syllabus->id}: " . $e->getMessage());
            }
        }

        return $results;
    }

    public function getDefaultChecklistForSyllabus(Syllabus $syllabus): QualityChecklist
    {
        // Try to get department-specific checklist first
        $checklist = QualityChecklist::active()
            ->where('department_id', $syllabus->course->college->departments()->first()?->id)
            ->where('is_default', true)
            ->first();

        // Fall back to college-specific checklist
        if (!$checklist) {
            $checklist = QualityChecklist::active()
                ->where('college_id', $syllabus->course->college_id)
                ->where('is_default', true)
                ->first();
        }

        // Fall back to institution-wide default checklist
        if (!$checklist) {
            $checklist = QualityChecklist::active()
                ->whereNull('college_id')
                ->whereNull('department_id')
                ->where('is_default', true)
                ->first();
        }

        // Create a basic checklist if none exists
        if (!$checklist) {
            $checklist = $this->createBasicQualityChecklist();
        }

        return $checklist;
    }

    public function createBasicQualityChecklist(): QualityChecklist
    {
        $checklist = QualityChecklist::create([
            'name' => 'Basic Quality Checklist',
            'description' => 'Automatically generated basic quality checklist',
            'type' => 'basic',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Create basic checklist items
        $basicItems = [
            [
                'title' => 'Syllabus Name Required',
                'description' => 'Syllabus must have a name',
                'field_to_check' => 'name',
                'validation_rule' => 'required',
                'weight' => 1.0,
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Course Description Required',
                'description' => 'Syllabus must have a description',
                'field_to_check' => 'description',
                'validation_rule' => 'required',
                'weight' => 1.0,
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Adequate Description Length',
                'description' => 'Course description should be at least 100 characters',
                'field_to_check' => 'description',
                'validation_rule' => 'min_length',
                'validation_parameters' => ['min_length' => 100],
                'weight' => 0.5,
                'sort_order' => 3,
            ],
            [
                'title' => 'Course Outcomes Present',
                'description' => 'Syllabus should have defined course outcomes',
                'field_to_check' => 'course_outcomes',
                'validation_rule' => 'array_min_items',
                'validation_parameters' => ['min_items' => 1],
                'weight' => 1.5,
                'is_mandatory' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Learning Matrix Present',
                'description' => 'Syllabus should have a learning matrix',
                'field_to_check' => 'learning_matrix',
                'validation_rule' => 'array_min_items',
                'validation_parameters' => ['min_items' => 1],
                'weight' => 1.5,
                'sort_order' => 5,
            ],
            [
                'title' => 'Grading System Defined',
                'description' => 'Syllabus should have a defined grading system',
                'field_to_check' => 'grading_system',
                'validation_rule' => 'required',
                'weight' => 1.0,
                'sort_order' => 6,
            ],
            [
                'title' => 'Classroom Policies Present',
                'description' => 'Syllabus should include classroom policies',
                'field_to_check' => 'classroom_policies',
                'validation_rule' => 'required',
                'weight' => 0.5,
                'sort_order' => 7,
            ],
        ];

        foreach ($basicItems as $itemData) {
            $checklist->items()->create($itemData);
        }

        return $checklist;
    }

    public function getQualityMetrics(array $filters = []): array
    {
        $query = SyllabusQualityCheck::with(['syllabus', 'qualityChecklist']);

        // Apply filters
        if (isset($filters['college_id'])) {
            $query->whereHas('syllabus.course.college', fn($q) => $q->where('id', $filters['college_id']));
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('syllabus.course.college.departments', fn($q) => $q->where('id', $filters['department_id']));
        }

        if (isset($filters['period_start']) && isset($filters['period_end'])) {
            $query->whereBetween('checked_at', [$filters['period_start'], $filters['period_end']]);
        }

        $qualityChecks = $query->get();

        $totalChecks = $qualityChecks->count();
        $passedChecks = $qualityChecks->where('status', 'passed')->count();
        $failedChecks = $qualityChecks->where('status', 'failed')->count();
        $averageScore = $qualityChecks->where('overall_score', '>', 0)->avg('overall_score') ?? 0;

        return [
            'total_checks' => $totalChecks,
            'passed_checks' => $passedChecks,
            'failed_checks' => $failedChecks,
            'requires_improvement' => $qualityChecks->where('status', 'requires_improvement')->count(),
            'pass_rate' => $totalChecks > 0 ? round(($passedChecks / $totalChecks) * 100, 2) : 0,
            'average_score' => round($averageScore, 2),
            'score_distribution' => $this->getScoreDistribution($qualityChecks),
            'common_failures' => $this->getCommonFailures($qualityChecks),
        ];
    }

    private function getScoreDistribution(Collection $qualityChecks): array
    {
        $distribution = [
            '90-100' => 0,
            '80-89' => 0,
            '70-79' => 0,
            '60-69' => 0,
            '0-59' => 0,
        ];

        foreach ($qualityChecks->where('overall_score', '>', 0) as $check) {
            $score = $check->overall_score;

            if ($score >= 90) $distribution['90-100']++;
            elseif ($score >= 80) $distribution['80-89']++;
            elseif ($score >= 70) $distribution['70-79']++;
            elseif ($score >= 60) $distribution['60-69']++;
            else $distribution['0-59']++;
        }

        return $distribution;
    }

    private function getCommonFailures(Collection $qualityChecks): array
    {
        $failures = [];

        foreach ($qualityChecks as $check) {
            $itemResults = $check->item_results ?? [];
            foreach ($itemResults as $result) {
                if ($result['status'] === 'failed') {
                    $title = $result['title'] ?? 'Unknown';
                    $failures[$title] = ($failures[$title] ?? 0) + 1;
                }
            }
        }

        arsort($failures);
        return array_slice($failures, 0, 10, true); // Top 10 common failures
    }

    public function scheduleAutomatedChecks(): void
    {
        // Get syllabi that need quality checks
        $syllabi = Syllabus::whereIn('status', ['pending_approval', 'dept_chair_review'])
            ->whereDoesntHave('qualityChecks', function ($query) {
                $query->where('auto_generated', true)
                      ->where('checked_at', '>=', now()->subDays(7)); // Check if checked in last 7 days
            })
            ->limit(50) // Process in batches
            ->get();

        foreach ($syllabi as $syllabus) {
            try {
                $this->runAutomatedQualityCheck($syllabus);
            } catch (\Exception $e) {
                \Log::error("Failed to run scheduled quality check for syllabus {$syllabus->id}: " . $e->getMessage());
            }
        }
    }
}