<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CardsVariantsTypesMv Model
 * 
 * Materialized view model pro rychlé načítání variant karet s jejich typy.
 * Kombinuje data z cards_variant a cards_variant_types tabulek.
 * Nahrazuje složité JOIN dotazy v CollectionItemService::getTypesForCard().
 * 
 * @property int $mv_id
 * @property string $card_id
 * @property int $cm_id
 * @property int $variant_type_code
 * @property string $variant_type_name
 * @property string|null $variant_type_description
 * @property string|null $price_column_suffix
 * @property int $variant
 * @property int|null $cm_expansion_id
 * @property int|null $cm_metacard_id
 * @property string|null $collector_number
 * @property string|null $ptcgo_code
 * @property int|null $tcgplayer_id
 * @property string|null $rarity
 * @property \Carbon\Carbon|null $date_added
 * @property bool $variant_normal
 * @property bool $variant_holo
 * @property bool $variant_reverse
 * @property bool $variant_promo
 * @property bool $variant_pokeball
 * @property bool $variant_masterball
 * @property bool $is_primary_variant
 * @property \Carbon\Carbon $last_updated
 * 
 * @property Card|null $card
 * @property CardsVariant|null $variant_model
 */
class CardsVariantsTypesMv extends Model
{
    use HasFactory;

    protected $table = 'cards_variants_types_mv';
    protected $primaryKey = 'mv_id';
    
    // View je read-only
    protected $guarded = ['*'];
    
    // Nepoužíváme standardní Laravel timestamps
    public $timestamps = false;

    protected $casts = [
        'mv_id' => 'integer',
        'cm_id' => 'integer',
        'variant_type_code' => 'integer',
        'variant' => 'integer',
        'cm_expansion_id' => 'integer',
        'cm_metacard_id' => 'integer',
        'tcgplayer_id' => 'integer',
        'date_added' => 'datetime',
        'variant_normal' => 'boolean',
        'variant_holo' => 'boolean',
        'variant_reverse' => 'boolean',
        'variant_promo' => 'boolean',
        'variant_pokeball' => 'boolean',
        'variant_masterball' => 'boolean',
        'is_primary_variant' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the card associated with this variant
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    /**
     * Get the original variant record
     */
    public function variant_model(): BelongsTo
    {
        return $this->belongsTo(CardsVariant::class, 'cm_id', 'cm_id');
    }

    /**
     * Scope pro rychlé načtení variant pro konkrétní kartu
     */
    public function scopeForCard($query, string $cardId)
    {
        return $query->where('card_id', $cardId);
    }

    /**
     * Scope pro načtení pouze primárních variant
     */
    public function scopePrimaryVariants($query)
    {
        return $query->where('is_primary_variant', true);
    }

    /**
     * Scope pro filtrování podle typu varianty
     */
    public function scopeVariantType($query, int $variantTypeCode)
    {
        return $query->where('variant_type_code', $variantTypeCode);
    }

    /**
     * Scope pro načtení variant s cenami (pokud má price_column_suffix)
     */
    public function scopeWithPricing($query)
    {
        return $query->whereNotNull('price_column_suffix');
    }

    /**
     * Rychlé načtení dostupných typů variant pro danou kartu
     * Nahrazuje CollectionItemService::getTypesForCard()
     */
    public static function getVariantTypesForCard(string $cardId): \Illuminate\Database\Eloquent\Collection
    {
        return static::forCard($cardId)
            ->select([
                'variant_type_code as code',
                'variant_type_name as name', 
                'variant_type_description as description'
            ])
            ->selectRaw('MIN(variant) as variant') // Pro kompatibilitu se stávajícím kódem
            ->groupBy('variant_type_code', 'variant_type_name', 'variant_type_description')
            ->orderBy('variant_type_code')
            ->get();
    }

    /**
     * Rychlé nalezení konkrétní varianty pro kartu a typ
     * Nahrazuje CollectionItemService::resolveVariantForType()
     */
    public static function resolveVariantForType(string $cardId, string $variantTypeCode): ?self
    {
        return static::forCard($cardId)
            ->variantType($variantTypeCode)
            ->primaryVariants() // Preferujeme primární variantu
            ->first();
    }

    /**
     * Rychlé načtení primární varianty pro kartu
     */
    public static function getPrimaryVariantForCard(string $cardId): ?self
    {
        return static::forCard($cardId)
            ->primaryVariants()
            ->orderBy('variant_type_code')
            ->first();
    }

    /**
     * Batch načtení variant pro více karet najednou
     */
    public static function getVariantsForCards(array $cardIds): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereIn('card_id', $cardIds)
            ->orderBy('card_id')
            ->orderBy('variant_type_code')
            ->get()
            ->groupBy('card_id');
    }

    /**
     * Získá statistiky materialized view
     */
    public static function getStats(): array
    {
        return [
            'total_records' => static::count(),
            'unique_cards' => static::distinct('card_id')->count('card_id'),
            'unique_variants' => static::distinct('cm_id')->count('cm_id'),
            'variant_types' => static::distinct('variant_type_code')->count('variant_type_code'),
            'last_updated' => static::max('last_updated'),
        ];
    }

    /**
     * Formatované zobrazení typu varianty
     */
    public function getFormattedVariantTypeAttribute(): string
    {
        return $this->variant_type_name . ($this->variant_type_description ? ' (' . $this->variant_type_description . ')' : '');
    }

    /**
     * Získá cm_id pro tuto variantu (alias pro kompatibilitu)
     */
    public function getVariantIdAttribute(): int
    {
        return $this->cm_id;
    }

    /**
     * Kontrola zda je to reverse holo varianta
     */
    public function getIsReverseHoloAttribute(): bool
    {
        return $this->price_column_suffix && str_contains(strtolower($this->price_column_suffix), 'reverse_holo');
    }
}
