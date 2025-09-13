<?php

namespace App\Models;

use App\Constants\SyllabusConstants;
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
        'default_classroom_policies',
        'default_consultation_hours',
        'default_grading_system',
        'principal_prepared_by',
        'prepared_by',
        'reviewed_by',
        'recommending_approval',
        'approved_by',
        'sort_order',
        'status',
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
        'sort_order' => 'integer',
        'course_id' => 'integer',
        'principal_prepared_by' => 'integer',
        'reviewed_by' => 'integer',
        'recommending_approval' => 'integer',
        'approved_by' => 'integer',
        'status' => 'string',
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
     */
    public function getPreparersAttribute()
    {
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
    }

    /**
     * Get all preparers and signers.
     */
    public function getAllSignersAttribute()
    {
        $signers = [];
        
        if ($this->principalPreparer) $signers['principal_prepared_by'] = $this->principalPreparer;
        
        // Add all preparers from the array
        foreach ($this->preparers as $index => $preparer) {
            $signers['prepared_by_' . ($index + 1)] = $preparer['user'];
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
}
