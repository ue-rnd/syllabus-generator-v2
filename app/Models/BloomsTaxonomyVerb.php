<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloomsTaxonomyVerb extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope a query to only include active verbs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by category and sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('sort_order')->orderBy('label');
    }

    /**
     * Get verbs grouped by category for select options.
     */
    public static function getOptionsGrouped(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('category')
            ->map(function ($verbs) {
                return $verbs->pluck('label', 'key')->toArray();
            })
            ->toArray();
    }

    /**
     * Get all verbs as flat options array for select fields.
     */
    public static function getOptions(): array
    {
        return static::active()
            ->ordered()
            ->pluck('label', 'key')
            ->toArray();
    }

    /**
     * Get category color for badges.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'Remember' => 'primary',
            'Understand' => 'success',
            'Apply' => 'warning',
            'Analyze' => 'purple',
            'Evaluate' => 'danger',
            'Create' => 'pink',
            default => 'gray',
        };
    }
}
