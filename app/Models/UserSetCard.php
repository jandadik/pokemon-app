<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Předpokládáme existenci těchto modelů:
use App\Models\User;
use App\Models\Set;
use App\Models\Card;
use App\Models\CardsVariant;
use App\Models\UserCollectionItem;
use App\Models\UserSetTracking; // Pro případnou inverzní relaci

class UserSetCard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_set_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'set_id',
        'card_id',
        'variant_id',
        'tracking_view',
        'status',
        'collection_item_id', // FK na kartu ve sbírce, která plní tento záznam
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Enumy není třeba castovat
    ];

    /**
     * Get the user associated with this card tracking record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the set associated with this card tracking record.
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class, 'set_id', 'id');
    }

    /**
     * Get the card associated with this tracking record.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    /**
     * Get the card variant associated with this tracking record.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(CardsVariant::class, 'variant_id', 'cm_id');
    }

    /**
     * Get the collection item that fulfills this tracked card (if any).
     */
    public function collectionItem(): BelongsTo
    {
        return $this->belongsTo(UserCollectionItem::class, 'collection_item_id');
    }

    /**
     * Get the parent UserSetTracking record.
     * Tato relace je složitá kvůli kompozitnímu klíči.
     */
    public function userSetTracking(): BelongsTo
    {
        // Standardní BelongsTo zde nebude fungovat správně.
        // return $this->belongsTo(UserSetTracking::class, ???);

        // Musíme najít UserSetTracking se stejným user_id, set_id a tracking_view.
        // Toto je efektivně dotaz, ne standardní Eloquent relace.
        return $this->belongsTo(UserSetTracking::class, 'user_id', 'user_id')
                    ->where('set_id', $this->set_id)
                    ->where('tracking_view', $this->tracking_view);

         // TODO: Zvážit lepší implementaci této komplexní relace.
    }

}
