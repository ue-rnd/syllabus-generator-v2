<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusSuggestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'syllabus_id',
        'suggested_by',
        'field_name',
        'current_value',
        'suggested_value',
        'reason',
        'status',
        'reviewed_at',
        'reviewed_by',
        'review_comments',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the syllabus that this suggestion belongs to.
     */
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    /**
     * Get the user who made this suggestion.
     */
    public function suggestedBy()
    {
        return $this->belongsTo(User::class, 'suggested_by');
    }

    /**
     * Get the user who reviewed this suggestion.
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope a query to only include pending suggestions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved suggestions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected suggestions.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to order by creation date (newest first).
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if the suggestion is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the suggestion is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the suggestion is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Approve this suggestion and apply the change.
     */
    public function approve(User $reviewer, ?string $comments = null): bool
    {
        if (! $this->isPending()) {
            return false;
        }

        // Apply the suggested change to the syllabus
        $this->syllabus->update([
            $this->field_name => $this->suggested_value,
        ]);

        // Mark suggestion as approved
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'review_comments' => $comments,
        ]);

        return true;
    }

    /**
     * Reject this suggestion.
     */
    public function reject(User $reviewer, string $comments): bool
    {
        if (! $this->isPending()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'review_comments' => $comments,
        ]);

        return true;
    }

    /**
     * Check if the user can review this suggestion.
     */
    public function canBeReviewedBy(User $user): bool
    {
        // Only the principal preparer can review suggestions
        return $user->id === $this->syllabus->principal_prepared_by;
    }

    /**
     * Get a human-readable description of the change.
     */
    public function getChangeDescriptionAttribute(): string
    {
        $fieldLabels = [
            'name' => 'Syllabus Name',
            'description' => 'Course Description',
            'course_outcomes' => 'Course Outcomes',
            'learning_matrix' => 'Learning Matrix',
            'textbook_references' => 'Textbook References',
            'adaptive_digital_solutions' => 'Adaptive Digital Solutions',
            'online_references' => 'Online References',
            'other_references' => 'Other References',
            'grading_system' => 'Grading System',
            'classroom_policies' => 'Classroom Policies',
            'consultation_hours' => 'Consultation Hours',
            'program_outcomes' => 'Program Outcomes',
        ];

        $fieldLabel = $fieldLabels[$this->field_name] ?? ucfirst(str_replace('_', ' ', $this->field_name));

        return "Change {$fieldLabel}";
    }

    /**
     * Get a preview of the suggested change.
     */
    public function getChangePreviewAttribute(): array
    {
        return [
            'field' => $this->field_name,
            'field_label' => $this->change_description,
            'current' => $this->current_value ? substr(strip_tags($this->current_value), 0, 100).'...' : '(empty)',
            'suggested' => substr(strip_tags($this->suggested_value), 0, 100).'...',
        ];
    }
}
