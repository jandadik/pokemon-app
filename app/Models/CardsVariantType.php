<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardsVariantType extends Model
{
    use HasFactory;

    protected $table = 'cards_variant_types';

    // Nepoužíváme Laravel timestamps (created_at, updated_at)
    public $timestamps = false;

    // Sloupce povolené pro mass assignment (použito v seederu)
    protected $fillable = [
        'code',
        'variant',
        'name',
        'price_column_suffix',
        'description',
    ];

    // Definice relací (pokud budou potřeba)
    // Např. pokud chceme získat všechny CardVariant záznamy daného typu:
    // public function card_variants()
    // {
    //     return $this->hasMany(CardsVariant::class, 'variant_type_id'); 
    // }
}
