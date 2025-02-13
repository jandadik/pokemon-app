<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Register extends Model
{
    protected $fillable = [
        'register_category_id',
        'name',
        'type',
        'default'
    ];

    protected $casts = [
        'default' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(RegisterCategory::class, 'register_category_id');
    }
} 