<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutorial_id',
        'title',
        'content',
        'step_order',
        'image_url',
        'video_url',
        'code_snippet',
        'notes',
    ];

    protected $casts = [
        'step_order' => 'integer',
    ];

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }
}
