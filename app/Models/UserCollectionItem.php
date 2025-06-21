<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // Pro vazbu na user_set_cards
// Předpokládáme existenci těchto modelů:
use App\Models\Card;
use App\Models\CardsVariant;
use App\Models\CardsVariantType;

class UserCollectionItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_collection_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_id',
        'card_id',
        'variant_id',
        'variant_type',
        'quantity',
        'condition',
        'language',
        'is_first_edition',
        'is_graded',
        'grade_company',
        'grade_value',
        'purchase_price',
        'purchase_date',
        'notes',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_first_edition' => 'boolean',
        'is_graded' => 'boolean',
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
        'quantity' => 'integer',
        // ENUMy není třeba explicitně castovat, pokud nechceme specifické objekty
    ];

    /**
     * Get the collection that owns the item.
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(UserCollection::class, 'collection_id');
    }

    /**
     * Get the card associated with the item.
     * Předpokládá existenci modelu Card.
     */
    public function card(): BelongsTo
    {
        // Předpokládá, že model Card používá 'id' jako primární klíč (VARCHAR)
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    /**
     * Get the card variant associated with the item.
     * Předpokládá existenci modelu CardsVariant.
     */
    public function variant(): BelongsTo
    {
        // Předpokládá, že model CardsVariant používá 'cm_id' jako primární klíč (INTEGER)
        return $this->belongsTo(CardsVariant::class, 'variant_id', 'cm_id');
    }

    /**
     * Get the variant type associated with the item.
     */
    public function variantType(): BelongsTo
    {
        return $this->belongsTo(CardsVariantType::class, 'variant_type', 'code');
    }

    /**
     * Get the user set card record that this item fulfills.
     * Toto je vazba 1:1 (nebo 1:0), protože jeden collection_item může splnit jeden user_set_card.
     */
    public function userSetCard(): HasOne
    {
        return $this->hasOne(UserSetCard::class, 'collection_item_id');
    }
}
