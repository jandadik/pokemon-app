<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardsPricesMv extends Model
{
    // Tento model reprezentuje tabulku cards_prices_mv,
    // která může být materializovaným pohledem nebo cache.
    // Nepoužíváme HasFactory, protože data se typicky negenerují přes factory.
    // use HasFactory;

    protected $table = 'cards_prices_mv';
    protected $primaryKey = 'card_id'; // Primární klíč podle migrace
    public $incrementing = false; // PK není auto-increment
    protected $keyType = 'string'; // PK je string (varchar)

    // Tabulka má pouze last_updated spravované DB, nikoli Eloquent timestamps
    public $timestamps = false;

    // Není třeba fillable, pokud data vkládáme přes DB::table() nebo procedurou
    // protected $fillable = []; 

    protected $casts = [
        'variant_id' => 'integer',
        'tcg_price_low' => 'decimal:2',
        'tcg_price_mid' => 'decimal:2',
        'tcg_price_high' => 'decimal:2',
        'tcg_price_market' => 'decimal:2',
        'tcg_price_direct_low' => 'decimal:2',
        'tcg_updated_at' => 'datetime',
        'cm_price_low' => 'decimal:2',
        'cm_price_trend' => 'decimal:2',
        'cm_price_avg' => 'decimal:2',
        'cm_price_suggested' => 'decimal:2',
        'cm_avg7' => 'decimal:2',
        'cm_avg30' => 'decimal:2',
        'cm_updated_at' => 'datetime',
        // 'last_updated' => 'datetime', // Spravováno DB
        'cm_reverse_holo_sell' => 'decimal:2',
        'cm_reverse_holo_low' => 'decimal:2',
        'cm_reverse_holo_trend' => 'decimal:2',
        'cm_reverse_holo_avg1' => 'decimal:2',
        'cm_reverse_holo_avg7' => 'decimal:2',
        'cm_reverse_holo_avg30' => 'decimal:2',
        'tcg_reverse_holo_price_low' => 'decimal:2',
        'tcg_reverse_holo_price_mid' => 'decimal:2',
        'tcg_reverse_holo_price_high' => 'decimal:2',
        'tcg_reverse_holo_price_market' => 'decimal:2',
        'tcg_reverse_holo_price_direct_low' => 'decimal:2',
        'tcg_reverse_holo_updated_at' => 'datetime',
    ];

    /**
     * Získání karty, ke které patří tyto ceny.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    /**
     * Získání setu, do kterého patří karta.
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class, 'set_id', 'id');
    }

    /**
     * Získání varianty karty (pokud je relevantní).
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(CardsVariant::class, 'variant_id', 'cm_id');
    }
}
