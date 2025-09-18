<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'attributes',
        'sort_order',
        'category',
    ];

    protected $casts = [
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];
}
