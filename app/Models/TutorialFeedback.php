<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutorial_id',
        'user_id',
        'is_helpful',
        'rating',
        'comment',
        'ip_address',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
        'rating' => 'integer',
    ];

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeHelpful($query)
    {
        return $query->where('is_helpful', true);
    }

    public function scopeNotHelpful($query)
    {
        return $query->where('is_helpful', false);
    }

    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }
}
