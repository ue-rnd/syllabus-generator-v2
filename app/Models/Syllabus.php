<?php

namespace App\Models;

use App\Constants\SyllabusConstants;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Syllabus extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'course_id',
        'default_lecture_hours',
        'default_laboratory_hours',
        'course_outcomes',
        'learning_matrix',
        'textbook_references',
        'adaptive_digital_solutions',
        'online_references',
        'other_references',
        'grading_system',
        'classroom_policies',
        'consultation_hours',
        'principal_prepared_by',
        'prepared_by',
        'reviewed_by',
        'recommending_approval',
        'approved_by',
        'sort_order',
        'status',
        'submitted_at',
        'dept_chair_reviewed_at',
        'assoc_dean_reviewed_at',
        'dean_approved_at',
        'approval_history',
        'rejection_comments',
        'rejected_by_role',
        'rejected_at',
        'parent_syllabus_id',
        'week_prelim',
        'week_midterm',
        'week_final',
        'ay_start',
        'ay_end',
        'program_outcomes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'default_lecture_hours' => 'decimal:1',
        'default_laboratory_hours' => 'decimal:1',
        'course_outcomes' => 'array',
        'learning_matrix' => 'array',
        'prepared_by' => 'array',
        'approval_history' => 'array',
        'sort_order' => 'integer',
        'course_id' => 'integer',
        'principal_prepared_by' => 'integer',
        'reviewed_by' => 'integer',
        'recommending_approval' => 'integer',
        'approved_by' => 'integer',
        'parent_syllabus_id' => 'integer',
        'version' => 'integer',
        'status' => 'string',
        'submitted_at' => 'datetime',
        'dept_chair_reviewed_at' => 'datetime',
        'assoc_dean_reviewed_at' => 'datetime',
        'dean_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'week_prelim' => 'integer',
        'week_midterm' => 'integer',
        'week_final' => 'integer',
        'ay_start' => 'integer',
        'ay_end' => 'integer',
        'program_outcomes' => 'array'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When a syllabus is being created, generate a name if not provided
        static::creating(function ($syllabus) {
            // Set principal_prepared_by to current authenticated user if not set
            if (empty($syllabus->principal_prepared_by) && auth()->check()) {
                $syllabus->principal_prepared_by = auth()->id();
            }
            
            if (empty($syllabus->name) && $syllabus->course_id) {
                $course = Course::find($syllabus->course_id);
                if ($course) {
                    $syllabus->name = $course->name . ' Syllabus';
                }
            }

            // Set default values for policy fields if not provided
            if (empty($syllabus->classroom_policies) && !empty($syllabus->default_classroom_policies)) {
                $syllabus->classroom_policies = $syllabus->default_classroom_policies;
            }
            if (empty($syllabus->consultation_hours) && !empty($syllabus->default_consultation_hours)) {
                $syllabus->consultation_hours = $syllabus->default_consultation_hours;
            }
            if (empty($syllabus->grading_system) && !empty($syllabus->default_grading_system)) {
                $syllabus->grading_system = $syllabus->default_grading_system;
            }
        });
    }

    /**
     * Scope a query to only include the latest (most recent) syllabus for each course.
     */
    public function scopeLatest($query)
    {
        return $query->whereIn('id', function($subquery) {
            $subquery->select('id')
                ->from('syllabi')
                ->whereNull('deleted_at')
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM syllabi s2 WHERE s2.course_id = syllabi.course_id AND s2.deleted_at IS NULL)');
        });
    }

    /**
     * Scope a query to order by creation date (newest first) and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name')->orderBy('created_at', 'desc');
    }

    /**
     * Check if the syllabus is the latest version for its course.
     */
    public function isLatest()
    {
        $latestSyllabus = static::where('course_id', $this->course_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $latestSyllabus && $latestSyllabus->id === $this->id;
    }

    /**
     * Get an accessor for checking if this is the latest version.
     */
    public function getIsLatestAttribute()
    {
        return $this->isLatest();
    }

    /**
     * Get the dynamic version number based on chronological order within the course.
     * This calculates the version by finding the position of this syllabus 
     * among all syllabi for the same course, ordered by creation date.
     */
    public function getVersionAttribute($value)
    {
        // If we're in the process of creating a new record, return the stored value or calculate
        if (!$this->exists) {
            return $value ?? $this->calculateNextVersionForCourse();
        }

        // For existing records, calculate the position based on creation date
        return $this->calculateVersionFromPosition();
    }

    /**
     * Calculate the version number based on the position in the chronological order.
     */
    private function calculateVersionFromPosition(): int
    {
        if (!$this->course_id || !$this->created_at) {
            return 1;
        }

        $position = static::where('course_id', $this->course_id)
            ->where('created_at', '<=', $this->created_at)
            ->where('id', '<=', $this->id) // In case of same timestamp, use ID as tiebreaker
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->count();

        return max(1, $position);
    }

    /**
     * Calculate what the next version number should be for a new syllabus in this course.
     */
    private function calculateNextVersionForCourse(): int
    {
        if (!$this->course_id) {
            return 1;
        }

        $maxVersion = static::where('course_id', $this->course_id)
            ->whereNull('deleted_at')
            ->count();

        return $maxVersion + 1;
    }

    /**
     * Get the course that owns this syllabus.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the principal preparer (current user who created it).
     */
    public function principalPreparer()
    {
        return $this->belongsTo(User::class, 'principal_prepared_by');
    }

    /**
     * Get the reviewer (department chair).
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the recommending approver (associate dean).
     */
    public function recommendingApprover()
    {
        return $this->belongsTo(User::class, 'recommending_approval');
    }

    /**
     * Get the final approver (dean).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all preparers with their roles/descriptions.
     * Note: This method is disabled to prevent memory issues in InfoLists.
     * Use the prepared_by JSON array directly instead.
     */
    public function getPreparersAttribute()
    {
        // Disabled to prevent memory exhaustion in Filament InfoLists
        // Use the prepared_by JSON array directly instead
        return collect();
        
        /* Original implementation commented out to prevent memory issues:
        if (empty($this->prepared_by)) {
            return collect();
        }

        $userIds = collect($this->prepared_by)->pluck('user_id')->filter();
        
        if ($userIds->isEmpty()) {
            return collect();
        }

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        return collect($this->prepared_by)->map(function ($preparer) use ($users) {
            $user = $users->get($preparer['user_id']);
            return [
                'user' => $user,
                'user_id' => $preparer['user_id'],
                'description' => $preparer['description'] ?? '',
                'role' => $preparer['role'] ?? '',
            ];
        })->filter(function ($preparer) {
            return $preparer['user'] !== null;
        });
        */
    }

    /**
     * Get all preparers and signers.
     * Note: Modified to prevent memory issues.
     */
    public function getAllSignersAttribute()
    {
        $signers = [];
        
        if ($this->principalPreparer) $signers['principal_prepared_by'] = $this->principalPreparer;
        
        // Use raw prepared_by data instead of accessor to prevent memory issues
        if (!empty($this->prepared_by)) {
            foreach ($this->prepared_by as $index => $preparer) {
                if (isset($preparer['user_id'])) {
                    $user = User::find($preparer['user_id']);
                    if ($user) {
                        $signers['prepared_by_' . ($index + 1)] = $user;
                    }
                }
            }
        }
        
        if ($this->reviewer) $signers['reviewed_by'] = $this->reviewer;
        if ($this->recommendingApprover) $signers['recommending_approval'] = $this->recommendingApprover;
        if ($this->approver) $signers['approved_by'] = $this->approver;

        return $signers;
    }

    /**
     * Check if syllabus is fully signed (has all required signers).
     */
    public function getIsFullySignedAttribute()
    {
        return $this->principal_prepared_by && 
               $this->reviewed_by && 
               $this->recommending_approval && 
               $this->approved_by;
    }

    /**
     * Get approval status.
     */
    public function getApprovalStatusAttribute()
    {
        if ($this->approved_by) {
            return 'Approved';
        } elseif ($this->recommending_approval) {
            return 'Pending Final Approval';
        } elseif ($this->reviewed_by) {
            return 'Pending Recommendation';
        } else {
            return 'Draft';
        }
    }

    /**
     * Get the full name with creation date.
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->created_at->format('M j, Y') . ')';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        return SyllabusConstants::STATUSES[$this->status] ?? 'Unknown';
    }

    /**
     * Get the active (latest) syllabus for a course.
     */
    public static function getActiveSyllabusForCourse($courseId)
    {
        return static::where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get all syllabus versions for a course (ordered by creation date).
     */
    public static function getAllVersionsForCourse($courseId)
    {
        return static::where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total hours from learning matrix using default hours.
     */
    public function getTotalHoursAttribute()
    {
        if (empty($this->learning_matrix)) {
            return ['lecture' => 0, 'laboratory' => 0, 'total' => 0];
        }

        // Count unique weeks and multiply by default hours
        $weeks = [];
        foreach ($this->learning_matrix as $item) {
            if (!empty($item['week_range'])) {
                $weekRange = $item['week_range'];
                if (isset($weekRange['is_range']) && $weekRange['is_range']) {
                    for ($w = $weekRange['start']; $w <= $weekRange['end']; $w++) {
                        $weeks[$w] = true;
                    }
                } else {
                    $weeks[$weekRange['start']] = true;
                }
            }
        }

        $totalWeeks = count($weeks);
        $totalLecture = $totalWeeks * $this->default_lecture_hours;
        $totalLab = $totalWeeks * $this->default_laboratory_hours;

        return [
            'lecture' => $totalLecture,
            'laboratory' => $totalLab,
            'total' => $totalLecture + $totalLab,
            'weeks' => $totalWeeks
        ];
    }

    /**
     * Validate week ranges to ensure they are sequential and non-overlapping.
     */
    public function validateWeekRanges()
    {
        if (empty($this->learning_matrix)) {
            return true;
        }

        $occupiedWeeks = [];
        $errors = [];

        foreach ($this->learning_matrix as $index => $item) {
            if (empty($item['week_range'])) {
                continue;
            }

            $weekRange = $item['week_range'];
            $start = $weekRange['start'] ?? null;
            $end = $weekRange['end'] ?? $start;

            if (!$start) {
                $errors[] = "Item " . ($index + 1) . ": Week range is required";
                continue;
            }

            if (isset($weekRange['is_range']) && $weekRange['is_range'] && $start > $end) {
                $errors[] = "Item " . ($index + 1) . ": Start week must be less than or equal to end week";
                continue;
            }

            // Check for overlaps
            for ($week = $start; $week <= $end; $week++) {
                if (isset($occupiedWeeks[$week])) {
                    $errors[] = "Week {$week} is used in multiple items";
                } else {
                    $occupiedWeeks[$week] = true;
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    // ============= APPROVAL WORKFLOW METHODS =============

    /**
     * Submit syllabus for approval
     */
    public function submitForApproval(User $user): bool
    {
        if (!$this->canSubmitForApproval($user)) {
            return false;
        }

        $this->update([
            'status' => 'pending_approval',
            'submitted_at' => now(),
        ]);

        $this->addToApprovalHistory('submitted', $user, 'Syllabus submitted for approval');

        return true;
    }

    /**
     * Approve syllabus at current stage
     */
    public function approve(User $user, ?string $comments = null): bool
    {
        if (!$this->canApprove($user)) {
            return false;
        }

        $nextStatus = $this->getNextApprovalStatus($user);
        $timestampField = $this->getTimestampField($user);

        $updateData = [
            'status' => $nextStatus,
        ];

        if ($timestampField) {
            $updateData[$timestampField] = now();
        }

        // Set the appropriate approver field
        $approverField = $this->getApproverField($user);
        if ($approverField) {
            $updateData[$approverField] = $user->id;
        }

        $this->update($updateData);

        // Generate appropriate approval message based on status
        $approvalMessage = $this->getApprovalMessage($user, $this->status);
        $this->addToApprovalHistory('approved', $user, $comments ?? $approvalMessage);

        return true;
    }

    /**
     * Reject syllabus with comments
     */
    public function reject(User $user, string $comments): bool
    {
        if (!$this->canReject($user)) {
            return false;
        }

        // For superadmin, show their position, otherwise use primary_role
        $rejectedByRole = $user->position === 'superadmin' ? 'superadmin' : $user->primary_role;

        $this->update([
            'status' => 'rejected',
            'rejection_comments' => $comments,
            'rejected_by_role' => $rejectedByRole,
            'rejected_at' => now(),
        ]);

        $this->addToApprovalHistory('rejected', $user, $comments);

        return true;
    }

    /**
     * Create a revision from rejected syllabus
     */
    public function createRevision(): self
    {
        $revision = $this->replicate();
        $revision->status = 'for_revisions';
        // Remove the manual version setting - it will be calculated dynamically
        $revision->parent_syllabus_id = $this->id;
        $revision->submitted_at = null;
        $revision->dept_chair_reviewed_at = null;
        $revision->assoc_dean_reviewed_at = null;
        $revision->dean_approved_at = null;
        $revision->approval_history = [];
        $revision->rejection_comments = null;
        $revision->rejected_by_role = null;
        $revision->rejected_at = null;
        
        $revision->save();

        return $revision;
    }

    /**
     * Check if user can submit for approval
     */
    public function canSubmitForApproval(User $user): bool
    {
        return in_array($this->status, ['draft', 'for_revisions']) && 
               ($user->id === $this->principal_prepared_by || $this->isUserInPreparedBy($user));
    }

    /**
     * Check if user can approve at current stage
     */
    public function canApprove(User $user): bool
    {
        // Check for superadmin position first
        if ($user->position === 'superadmin') {
            return in_array($this->status, ['pending_approval', 'dept_chair_review', 'assoc_dean_review', 'dean_review']);
        }

        $roleStatusMap = [
            'department_chair' => ['pending_approval', 'dept_chair_review'],
            'associate_dean' => ['assoc_dean_review'],
            'dean' => ['dean_review'],
        ];

        $userRole = $user->primary_role;
        $allowedStatuses = $roleStatusMap[$userRole] ?? [];

        return in_array($this->status, $allowedStatuses);
    }

    /**
     * Check if user can reject
     */
    public function canReject(User $user): bool
    {
        return $this->canApprove($user);
    }

    /**
     * Get next approval status based on current user role
     */
    private function getNextApprovalStatus(User $user): string
    {
        // Superadmin progresses through each step sequentially
        if ($user->position === 'superadmin') {
            $superadminTransitions = [
                'pending_approval' => 'dept_chair_review',
                'dept_chair_review' => 'assoc_dean_review',
                'assoc_dean_review' => 'dean_review',
                'dean_review' => 'approved',
            ];
            return $superadminTransitions[$this->status] ?? $this->status;
        }

        $transitions = [
            'department_chair' => [
                'pending_approval' => 'dept_chair_review',
                'dept_chair_review' => 'assoc_dean_review',
            ],
            'associate_dean' => [
                'assoc_dean_review' => 'dean_review',
            ],
            'dean' => [
                'dean_review' => 'approved',
            ],
        ];

        return $transitions[$user->primary_role][$this->status] ?? $this->status;
    }

    /**
     * Get timestamp field for approval tracking
     */
    private function getTimestampField(User $user): ?string
    {
        // For superadmin, determine field based on current status
        if ($user->position === 'superadmin') {
            return match ($this->status) {
                'pending_approval' => 'dept_chair_reviewed_at',
                'dept_chair_review' => 'assoc_dean_reviewed_at',
                'assoc_dean_review' => 'dean_approved_at',
                default => null,
            };
        }

        return match ($user->primary_role) {
            'department_chair' => 'dept_chair_reviewed_at',
            'associate_dean' => 'assoc_dean_reviewed_at',
            'dean' => 'dean_approved_at',
            default => null,
        };
    }

    /**
     * Get approver field for relationship tracking
     */
    private function getApproverField(User $user): ?string
    {
        // For superadmin, determine field based on current status
        if ($user->position === 'superadmin') {
            return match ($this->status) {
                'pending_approval' => 'reviewed_by',
                'dept_chair_review' => 'recommending_approval',
                'assoc_dean_review' => 'approved_by',
                default => null,
            };
        }

        return match ($user->primary_role) {
            'department_chair' => 'reviewed_by',
            'associate_dean' => 'recommending_approval',
            'dean' => 'approved_by',
            default => null,
        };
    }

    /**
     * Get appropriate approval message based on user role and status
     */
    private function getApprovalMessage(User $user, string $currentStatus): string
    {
        // Special handling for superadmin position
        if ($user->position === 'superadmin') {
            return match ($currentStatus) {
                'pending_approval' => 'External review completed (Super Admin)',
                'dept_chair_review' => 'Approved by Super Admin (Department Chair level)',
                'assoc_dean_review' => 'Approved by Super Admin (Associate Dean level)',
                'dean_review' => 'Approved by Super Admin (Dean level)',
                default => 'Approved by Super Admin',
            };
        }

        return match ([$user->primary_role, $currentStatus]) {
            ['department_chair', 'pending_approval'] => 'External review completed',
            ['department_chair', 'dept_chair_review'] => 'Approved by Department Chair',
            ['associate_dean', 'assoc_dean_review'] => 'Approved by Associate Dean',
            ['dean', 'dean_review'] => 'Approved by Dean',
            default => 'Approved at ' . $user->primary_role . ' level',
        };
    }

    /**
     * Add entry to approval history
     */
    private function addToApprovalHistory(string $action, User $user, ?string $comments = null): void
    {
        $history = $this->approval_history ?? [];

        // For superadmin, show their position, otherwise use primary_role
        $userRole = $user->position === 'superadmin' ? 'superadmin' : $user->primary_role;

        $history[] = [
            'action' => $action,
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'user_role' => $userRole,
            'comments' => $comments,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['approval_history' => $history]);
    }

    /**
     * Check if user is in prepared_by array
     */
    private function isUserInPreparedBy(User $user): bool
    {
        if (empty($this->prepared_by)) {
            return false;
        }

        return collect($this->prepared_by)->contains('user_id', $user->id);
    }

    /**
     * Get syllabus approval status with details
     */
    public function getApprovalStatusDetails(): array
    {
        return [
            'status' => $this->status,
            'status_label' => SyllabusConstants::STATUSES[$this->status] ?? 'Unknown',
            'status_color' => SyllabusConstants::getStatusColor($this->status),
            'submitted_at' => $this->submitted_at,
            'dept_chair_reviewed_at' => $this->dept_chair_reviewed_at,
            'assoc_dean_reviewed_at' => $this->assoc_dean_reviewed_at,
            'dean_approved_at' => $this->dean_approved_at,
            'rejected_at' => $this->rejected_at,
            'rejected_by_role' => $this->rejected_by_role,
            'rejection_comments' => $this->rejection_comments,
            'approval_history' => $this->approval_history ?? [],
            'version' => $this->version, // This will use the dynamic accessor
        ];
    }

    /**
     * Parent syllabus relationship (for revisions)
     */
    public function parentSyllabus()
    {
        return $this->belongsTo(self::class, 'parent_syllabus_id');
    }

    /**
     * Child syllabi relationship (revisions)
     */
    public function revisions()
    {
        return $this->hasMany(self::class, 'parent_syllabus_id');
    }
}