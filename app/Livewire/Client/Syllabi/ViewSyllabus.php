<?php

namespace App\Livewire\Client\Syllabi;

use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.client.dashboard.base')]
class ViewSyllabus extends Component
{
    public Syllabus $syllabus;

    public bool $showConfirmModal = false;

    public bool $showResultModal = false;

    public bool $resultSuccess = false;

    public ?string $resultMessage = null;

    public array $resultErrors = [];

    public function mount(Syllabus $syllabus): void
    {
        $this->syllabus = $syllabus->load(['course.college', 'course.programs']);
    }

    public function submitForApproval()
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        // Strict validation before allowing submission
        $errors = $this->validateForApproval();
        if (! empty($errors)) {
            session()->flash('error', 'Please complete all required fields before submitting for approval. '.implode(' ', array_slice($errors, 0, 3)));

            return null;
        }

        if ($this->syllabus->submitForApproval($user)) {
            session()->flash('success', 'Syllabus submitted for approval.');
        } else {
            session()->flash('error', 'Unable to submit syllabus for approval.');
        }

        $this->syllabus->refresh();

        return $this->redirectRoute('syllabus.view', $this->syllabus);
    }

    public function confirmSubmitForApproval(): void
    {
        $this->resultErrors = [];
        $this->resultSuccess = false;
        $this->resultMessage = null;
        $this->showConfirmModal = true;
    }

    public function performSubmitForApproval(): void
    {
        $this->showConfirmModal = false;
        $this->resultErrors = $this->validateForApproval();

        if (! empty($this->resultErrors)) {
            $this->resultSuccess = false;
            $this->resultMessage = 'Please address the following issues before submitting:';
            $this->showResultModal = true;

            return;
        }

        $user = Auth::user();
        if ($user && $this->syllabus->submitForApproval($user)) {
            $this->resultSuccess = true;
            $this->resultMessage = 'Syllabus submitted for approval successfully.';
            $this->syllabus->refresh();
        } else {
            $this->resultSuccess = false;
            $this->resultMessage = 'Unable to submit syllabus for approval.';
        }

        $this->showResultModal = true;
    }

    public function duplicateSyllabus()
    {
        try {
            // Create a new syllabus based on the current one
            $newSyllabus = $this->syllabus->replicate();

            // Update the name to indicate it's a copy
            $newSyllabus->name = $this->syllabus->name.' (Copy)';

            // Reset status and approval fields for the duplicate
            $newSyllabus->status = 'draft';
            $newSyllabus->submitted_at = null;
            $newSyllabus->dept_chair_reviewed_at = null;
            $newSyllabus->assoc_dean_reviewed_at = null;
            $newSyllabus->dean_approved_at = null;
            $newSyllabus->approval_history = null;
            $newSyllabus->rejection_comments = null;
            $newSyllabus->rejected_by_role = null;
            $newSyllabus->rejected_at = null;
            $newSyllabus->reviewed_by = null;
            $newSyllabus->recommending_approval = null;
            $newSyllabus->approved_by = null;

            // Set the principal preparer to the current user
            $newSyllabus->principal_prepared_by = Auth::id();

            // Clear the prepared_by array to avoid conflicts
            $newSyllabus->prepared_by = [];

            // Set parent_syllabus_id to track the original
            $newSyllabus->parent_syllabus_id = $this->syllabus->id;

            // Save the duplicate
            $newSyllabus->save();

            session()->flash('success', 'Syllabus duplicated successfully. You can now edit the copy.');

            // Redirect to the edit page of the new syllabus
            return $this->redirectRoute('syllabus.edit', $newSyllabus);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to duplicate syllabus: '.$e->getMessage());

            return;
        }
    }

    private function validateForApproval(): array
    {
        $s = $this->syllabus;
        $data = [
            'ay_start' => $s->ay_start,
            'ay_end' => $s->ay_end,
            'week_prelim' => $s->week_prelim,
            'week_midterm' => $s->week_midterm,
            'week_final' => $s->week_final,
            'course_id' => $s->course_id,
            'name' => $s->name,
            'description' => $s->description,
            'program_outcomes' => $s->program_outcomes ?? [],
            'course_outcomes' => $s->course_outcomes ?? [],
            'default_lecture_hours' => $s->default_lecture_hours,
            'default_laboratory_hours' => $s->default_laboratory_hours,
            'learning_matrix' => $s->learning_matrix ?? [],
            'textbook_references' => $s->textbook_references,
            'adaptive_digital_solutions' => $s->adaptive_digital_solutions,
            'online_references' => $s->online_references,
            'other_references' => $s->other_references,
            'grading_system' => $s->grading_system,
            'classroom_policies' => $s->classroom_policies,
            'consultation_hours' => $s->consultation_hours,
            'principal_prepared_by' => $s->principal_prepared_by,
            'reviewed_by' => $s->reviewed_by,
            'recommending_approval' => $s->recommending_approval,
            'approved_by' => $s->approved_by,
        ];

        $rules = [
            'ay_start' => 'required|numeric',
            'ay_end' => 'required|numeric',
            'week_prelim' => 'required|numeric|min:1|max:20',
            'week_midterm' => 'required|numeric|min:1|max:20',
            'week_final' => 'required|numeric|min:1|max:20',
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program_outcomes' => 'required|array|min:1',
            'program_outcomes.*.addressed' => 'required|array|min:1',
            'program_outcomes.*.addressed.*' => 'required|string|in:introduced,enhanced,demonstrated',
            'course_outcomes' => 'required|array|min:1',
            'course_outcomes.*.verb' => 'required|string',
            'course_outcomes.*.content' => 'required|string|min:10',
            'default_lecture_hours' => 'nullable|numeric|min:0',
            'default_laboratory_hours' => 'nullable|numeric|min:0',
            'learning_matrix' => 'required|array|min:1',
            'learning_matrix.*.week_range.is_range' => 'nullable|boolean',
            'learning_matrix.*.week_range.start' => 'required|integer|min:1|max:20',
            'learning_matrix.*.week_range.end' => 'nullable|integer|min:1|max:20',
            'learning_matrix.*.content' => 'required|string|min:3',
            'textbook_references' => 'nullable|string',
            'adaptive_digital_solutions' => 'nullable|string',
            'online_references' => 'nullable|string',
            'other_references' => 'nullable|string',
            'grading_system' => 'required|string|min:10',
            'classroom_policies' => 'required|string|min:10',
            'consultation_hours' => 'nullable|string',
            'principal_prepared_by' => 'required|integer|exists:users,id',
            'reviewed_by' => 'required|integer|exists:users,id',
            'recommending_approval' => 'required|integer|exists:users,id',
            'approved_by' => 'required|integer|exists:users,id',
        ];

        $messages = [
            'course_outcomes.required' => 'Please add at least one course learning outcome.',
            'course_outcomes.min' => 'Please add at least one course learning outcome.',
            'course_outcomes.*.verb.required' => 'Each course outcome must have an action verb.',
            'course_outcomes.*.content.required' => 'Each course outcome must have a description.',
            'course_outcomes.*.content.min' => 'Each course outcome description must be at least 10 characters.',

            'learning_matrix.required' => 'Please add at least one learning matrix item.',

            'reviewed_by.required' => 'Verified By (Department Chair) is required.',
            'recommending_approval.required' => 'Recommending Approval (Associate Dean) is required.',
            'approved_by.required' => 'Approved By (Dean) is required.',

            'program_outcomes.required' => 'Program outcomes are required.',
            'program_outcomes.min' => 'Program outcomes are required.',
            'program_outcomes.*.addressed.required' => 'Please select how each program outcome is addressed.',
            'program_outcomes.*.addressed.min' => 'Please select at least one addressing method for each program outcome.',
            'program_outcomes.*.addressed.*.in' => 'Please select a valid addressing method for program outcomes.',

            'grading_system.required' => 'Please define the grading system.',
            'classroom_policies.required' => 'Please define classroom policies.',
        ];

        $validator = Validator::make($data, $rules, $messages);
        $errors = [];
        if ($validator->fails()) {
            $errors = array_values(array_unique($validator->errors()->all()));
        }

        // Use model overlap validator
        $rangeCheck = $this->syllabus->validateWeekRanges();
        if ($rangeCheck !== true && is_array($rangeCheck)) {
            $errors = array_values(array_unique(array_merge($errors, $rangeCheck)));
        }

        // Dynamic cross-field checks involving week_final
        $weekFinal = intval($s->week_final);
        if ($weekFinal > 0) {
            // Ensure all week numbers are within 1..week_final and coverage is complete
            $covered = [];
            foreach (($data['learning_matrix'] ?? []) as $idx => $item) {
                $range = $item['week_range'] ?? [];
                $start = isset($range['start']) ? intval($range['start']) : null;
                $isRange = (bool) ($range['is_range'] ?? false);
                $end = $isRange ? intval($range['end'] ?? 0) : $start;

                if ($start !== null) {
                    if ($start < 1 || $start > $weekFinal) {
                        $errors[] = 'Learning matrix item '.($idx + 1).": Week start must be between 1 and {$weekFinal}.";
                    }
                }
                if ($isRange) {
                    if ($end < $start) {
                        $errors[] = 'Learning matrix item '.($idx + 1).': Week end must be greater than or equal to week start.';
                    }
                    if ($end > $weekFinal) {
                        $errors[] = 'Learning matrix item '.($idx + 1).": Week end must not exceed {$weekFinal}.";
                    }
                }

                if ($start !== null) {
                    $to = $isRange ? $end : $start;
                    for ($w = $start; $w <= $to; $w++) {
                        $covered[$w] = true;
                    }
                }
            }

            // Coverage completeness from week 1 to week_final
            $missing = [];
            for ($w = 1; $w <= $weekFinal; $w++) {
                if (! isset($covered[$w])) {
                    $missing[] = $w;
                }
            }
            if (! empty($missing)) {
                $errors[] = 'Learning matrix must cover all weeks from 1 to '.$weekFinal.'. Missing weeks: '.implode(', ', array_slice($missing, 0, 10)).(count($missing) > 10 ? '…' : '');
            }
        }

        return $errors;
    }

    public function render()
    {
        $user = Auth::user();
        $canEdit = false;
        $canSubmit = false;

        if ($user) {
            $canEdit = in_array($this->syllabus->status, ['draft', 'for_revisions']) && (
                $this->syllabus->principal_prepared_by === $user->id ||
                collect($this->syllabus->prepared_by ?? [])->contains('user_id', $user->id)
            );
            $canSubmit = $this->syllabus->canSubmitForApproval($user);
        }

        // Build prerequisites list from course
        $prerequisites = [];
        try {
            $prerequisites = $this->syllabus->course?->prerequisiteCourses()?->map(function ($course) {
                return [
                    'code' => $course->code,
                    'name' => $course->name,
                ];
            })->values()->toArray() ?? [];
        } catch (\Throwable $e) {
            $prerequisites = [];
        }

        // Build co-editor display data
        $coEditors = [];
        $preparedBy = $this->syllabus->prepared_by ?? [];
        if (is_array($preparedBy) && ! empty($preparedBy)) {
            $userIds = collect($preparedBy)
                ->pluck('user_id')
                ->filter()
                ->unique()
                ->values();
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            $coEditors = collect($preparedBy)->map(function ($entry) use ($users) {
                $userId = $entry['user_id'] ?? null;
                $user = $userId ? ($users[$userId] ?? null) : null;

                return [
                    'name' => $user?->full_name ?? $user?->name ?? '[Unknown User]',
                    'role' => $entry['role'] ?? null,
                    'description' => $entry['description'] ?? null,
                ];
            })->toArray();
        }

        return view('livewire.client.syllabi.view-syllabus', [
            'syllabus' => $this->syllabus,
            'canEdit' => $canEdit,
            'canSubmit' => $canSubmit,
            'prerequisites' => $prerequisites,
            'coEditors' => $coEditors,
        ]);
    }
}
