<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisterCategory extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];

    public function registers(): HasMany
    {
        return $this->hasMany(Register::class);
    }
} 