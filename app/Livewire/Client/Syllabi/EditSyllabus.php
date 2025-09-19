<?php

namespace App\Livewire\Client\Syllabi;

use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('livewire.client.dashboard.base')]
class EditSyllabus extends CreateSyllabus
{
    public Syllabus $syllabus;

    public function mount(): void
    {
        // Call parent to set defaults, then override with actual values
        parent::mount();

        $this->syllabus = $this->syllabus->load(['course.programs.department', 'course.college']);

        if (!$this->canEdit($this->syllabus)) {
            abort(403, 'You are not allowed to edit this syllabus.');
        }

        $this->ay_start = $this->syllabus->ay_start;
        $this->ay_end = $this->syllabus->ay_end;
        $this->week_prelim = $this->syllabus->week_prelim;
        $this->week_midterm = $this->syllabus->week_midterm;
        $this->week_final = $this->syllabus->week_final;

        $this->course_id = $this->syllabus->course_id;
        $this->name = $this->syllabus->name;
        // For editing, preserve HTML formatting but clean whitespace
        $rawDescription = $this->syllabus->description ?? '';
        // Remove leading/trailing whitespace and normalize excessive whitespace while preserving HTML structure
        $this->description = preg_replace('/\s+/', ' ', trim($rawDescription));

        $this->course = $this->syllabus->course;
        $this->program_outcomes = $this->syllabus->program_outcomes ?? [];
        $this->course_outcomes = $this->syllabus->course_outcomes ?? [];

        $this->default_lecture_hours = $this->syllabus->default_lecture_hours ?? 0.0;
        $this->default_laboratory_hours = $this->syllabus->default_laboratory_hours ?? 0.0;
        $this->learning_matrix = $this->syllabus->learning_matrix ?? [];

        $this->textbook_references = $this->syllabus->textbook_references ?? '';
        $this->adaptive_digital_solutions = $this->syllabus->adaptive_digital_solutions ?? '';
        $this->online_references = $this->syllabus->online_references ?? '';
        $this->other_references = $this->syllabus->other_references ?? '';

        $this->grading_system = $this->syllabus->grading_system ?? '';
        $this->classroom_policies = $this->syllabus->classroom_policies ?? '';
        $this->consultation_hours = $this->syllabus->consultation_hours ?? '';

        $this->principal_prepared_by = $this->syllabus->principal_prepared_by;
        $this->prepared_by = $this->syllabus->prepared_by ?? [];
        $this->reviewed_by = $this->syllabus->reviewed_by;
        $this->recommending_approval = $this->syllabus->recommending_approval;
        $this->approved_by = $this->syllabus->approved_by;
    }

    protected function canEdit(Syllabus $syllabus): bool
    {
        $userId = Auth::id();

        if (!in_array($syllabus->status, ['draft', 'for_revisions'])) {
            return false;
        }

        if ($syllabus->principal_prepared_by === $userId) {
            return true;
        }

        $preparedBy = collect($syllabus->prepared_by ?? []);
        return $preparedBy->contains(function ($item) use ($userId) {
            return isset($item['user_id']) && intval($item['user_id']) === intval($userId);
        });
    }

    public function confirmUpdate(): void
    {
        $this->showConfirmModal = true;
    }

    public function update()
    {
        // Reuse full validation from creator
        $this->submitValidationOnly();

        // Do not change status or approval timestamps on edit
        $this->syllabus->update([
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
        return $this->redirectRoute('home');
    }

    private function submitValidationOnly(): void
    {
        // Duplicate of parent submit() rules, without creating a new record
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
            'program_outcomes.*.addressed.*' => 'required|string|in:introduced,enhanced,demonstrated',
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
        ]);

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
    }

    public function render()
    {
        // Reuse parent's render variables but swap view
        $view = parent::render();
        $data = $view->getData();
        return view('livewire.client.syllabi.edit-syllabus', $data);
    }
}


