<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDraft extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'form_key',
        'current_step',
        'data',
        'version',
        'lock_token',
        'locked_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'current_step' => 'integer',
        'version' => 'integer',
        'locked_at' => 'datetime',
    ];

    /**
     * Get the user that owns the draft.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to find draft by user and form key.
     */
    public function scopeForUserAndForm($query, $userId, $formKey)
    {
        return $query->where('user_id', $userId)->where('form_key', $formKey);
    }

    /**
     * Scope to find drafts older than specified days.
     */
    public function scopeOlderThan($query, $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
}