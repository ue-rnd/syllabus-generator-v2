<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'tags',
        'is_published',
        'is_featured',
        'sort_order',
        'author_id',
        'views_count',
        'helpful_count',
        'not_helpful_count',
        'last_updated_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function feedbacks()
    {
        return $this->hasMany(FaqFeedback::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('question');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%")
                ->orWhereJsonContains('tags', $search);
        });
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'general' => 'primary',
            'syllabus' => 'success',
            'quality_assurance' => 'warning',
            'user_account' => 'info',
            'technical' => 'danger',
            'backup_recovery' => 'purple',
            default => 'gray',
        };
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
        // For FAQs, we'll just increment the view count
        $this->increment('views_count');
    }

    public static function getCategoryOptions(): array
    {
        return [
            'general' => 'General',
            'getting_started' => 'Getting Started',
            'syllabus' => 'Syllabus Management',
            'quality_assurance' => 'Quality Assurance',
            'user_account' => 'User Account',
            'permissions' => 'Permissions & Roles',
            'technical' => 'Technical Issues',
            'backup_recovery' => 'Backup & Recovery',
            'reporting' => 'Reporting & Analytics',
            'troubleshooting' => 'Troubleshooting',
        ];
    }
}