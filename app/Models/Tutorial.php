<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutorial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'category',
        'difficulty_level',
        'duration_minutes',
        'video_url',
        'attachments',
        'tags',
        'is_published',
        'sort_order',
        'featured',
        'author_id',
        'views_count',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'attachments' => 'array',
        'tags' => 'array',
        'is_published' => 'boolean',
        'featured' => 'boolean',
        'views_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function steps()
    {
        return $this->hasMany(TutorialStep::class)->orderBy('step_order');
    }

    public function views()
    {
        return $this->hasMany(TutorialView::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(TutorialFeedback::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficulty_level) {
            'beginner' => 'success',
            'intermediate' => 'warning',
            'advanced' => 'danger',
            default => 'gray',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'getting_started' => 'primary',
            'syllabus_creation' => 'success',
            'quality_assurance' => 'warning',
            'user_management' => 'info',
            'reporting' => 'purple',
            'troubleshooting' => 'danger',
            default => 'gray',
        };
    }

    public function getEstimatedReadTimeAttribute(): string
    {
        if ($this->duration_minutes) {
            return "{$this->duration_minutes} minutes";
        }

        // Estimate based on content length (average reading speed: 200 words per minute)
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);

        return "{$minutes} min read";
    }

    public function getHelpfulPercentageAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;

        if ($total === 0) {
            return 0;
        }

        return round(($this->helpful_count / $total) * 100, 1);
    }

    public function incrementViews(?User $user = null): void
    {
        // Check if user already viewed this tutorial today
        if ($user) {
            $existingView = $this->views()
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existingView) {
                $this->views()->create([
                    'user_id' => $user->id,
                    'ip_address' => request()->ip(),
                ]);
                $this->increment('views_count');
            }
        } else {
            // For anonymous users, just increment the counter
            $this->increment('views_count');
        }
    }

    public static function getCategoryOptions(): array
    {
        return [
            'getting_started' => 'Getting Started',
            'syllabus_creation' => 'Syllabus Creation',
            'quality_assurance' => 'Quality Assurance',
            'user_management' => 'User Management',
            'reporting' => 'Reporting & Analytics',
            'backup_recovery' => 'Backup & Recovery',
            'troubleshooting' => 'Troubleshooting',
            'advanced_features' => 'Advanced Features',
        ];
    }

    public static function getDifficultyOptions(): array
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        ];
    }
}