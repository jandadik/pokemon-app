<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetTrackingRule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'set_tracking_rules';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; // Ponecháme timestamps podle migrace

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tracking_view',
        'rarity',
        'variant_type',
        'series_from',
        'series_to',
        'include_in_printed_range',
        'include_above_printed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'include_in_printed_range' => 'boolean',
        'include_above_printed' => 'boolean',
        // Enum variant_type není třeba castovat
    ];

    // Tento model nemá přímé Eloquent relace k ostatním novým modelům.
    // Slouží jako konfigurační tabulka pro logiku vytváření UserSetCard záznamů.
}
