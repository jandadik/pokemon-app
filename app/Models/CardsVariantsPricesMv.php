<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardsVariantsPricesMv extends Model
{
    // Tento model reprezentuje tabulku cards_variants_prices_mv,
    // která může být materializovaným pohledem nebo cache.
    // Nepoužíváme HasFactory.
    // use HasFactory;

    protected $table = 'cards_variants_prices_mv';
    protected $primaryKey = 'cm_id'; // Primární klíč podle migrace
    public $incrementing = false; // PK není auto-increment
    protected $keyType = 'integer'; // PK je integer

    // Tabulka má pouze last_updated spravované DB, nikoli Eloquent timestamps
    public $timestamps = false;

    // Není třeba fillable
    // protected $fillable = [];

    protected $casts = [
        'card_id' => 'string', // I když je to FK, Laravel to může potřebovat
        'cm_expansion_id' => 'integer',
        'cm_metacard_id' => 'integer',
        'variant_normal' => 'boolean',
        'variant_holo' => 'boolean',
        'variant_reverse' => 'boolean',
        'variant_promo' => 'boolean',
        'variant' => 'integer',
        'variant_pokeball' => 'boolean',
        'variant_masterball' => 'boolean',
        'is_primary_variant' => 'boolean',
        'average_sell_price' => 'decimal:2',
        'low_price' => 'decimal:2',
        'trend_price' => 'decimal:2',
        'german_pro_low' => 'decimal:2',
        'suggested_price' => 'decimal:2',
        'reverse_holo_sell' => 'decimal:2',
        'reverse_holo_low' => 'decimal:2',
        'reverse_holo_trend' => 'decimal:2',
        'low_price_ex_plus' => 'decimal:2',
        'avg1' => 'decimal:2',
        'avg7' => 'decimal:2',
        'avg30' => 'decimal:2',
        'reverse_holo_avg1' => 'decimal:2',
        'reverse_holo_avg7' => 'decimal:2',
        'reverse_holo_avg30' => 'decimal:2',
        'cm_updated_at' => 'datetime',
        // 'last_updated' => 'datetime',
    ];

    /**
     * Získání varianty karty, ke které patří tyto ceny.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(CardsVariant::class, 'cm_id', 'cm_id'); // Vlastní FK je zároveň PK
    }
    
    /**
     * Získání "základní" karty (pokud je card_id vyplněno).
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }
}
