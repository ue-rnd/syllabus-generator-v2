<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_id',
        'user_id',
        'is_helpful',
        'comment',
        'ip_address',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class);
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
}
