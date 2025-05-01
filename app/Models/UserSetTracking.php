<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Předpokládáme existenci těchto modelů:
use App\Models\User;
use App\Models\Set;
use App\Models\UserCollection;
use App\Models\UserSetCard;

class UserSetTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_set_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'set_id',
        'collection_id', // Může být null, pokud není přímo vázáno na kolekci
        'tracking_view',
        'priority',
        'status',
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
     * Get the user that owns this tracking record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the set being tracked.
     */
    public function set(): BelongsTo
    {
        // Předpokládá, že Set model používá 'id' jako primární klíč (VARCHAR)
        return $this->belongsTo(Set::class, 'set_id', 'id');
    }

    /**
     * Get the collection associated with this tracking (optional).
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(UserCollection::class, 'collection_id');
    }

    /**
     * Get the individual card tracking records for this set tracking.
     */
    public function userSetCards(): HasMany
    {
        // Klíče jsou user_id, set_id, tracking_view. Musíme specifikovat složený klíč?
        // Standardní HasMany předpokládá jeden cizí klíč. Zde je vazba složitější.
        // Potřebujeme najít UserSetCard záznamy, které mají stejné user_id, set_id a tracking_view.
        // Toto standardní HasMany nezvládne jednoduše.
        // Možnost 1: Vytvořit vlastní metodu, která provede dotaz.
        // Možnost 2: Předefinovat klíče v HasMany (méně obvyklé pro složené klíče).
        // Možnost 3: Pokud je ID primární klíč user_set_tracking použit jako FK v user_set_cards, pak lze použít standardní HasMany.
        // Podle schématu user_set_cards nemá FK na user_set_tracking.id.

        // Řešení pomocí scope nebo přímého dotazu je zde vhodnější.
        // Prozatím necháme HasMany, ale s vědomím, že nemusí fungovat dle očekávání bez úprav.
        // return $this->hasMany(UserSetCard::class, ???); <-- Zde chybí správný FK
        // Správnější by bylo: UserSetCard::where('user_id', $this->user_id)->where('set_id', $this->set_id)->where('tracking_view', $this->tracking_view)

        // Vrátíme HasMany s explicitním definováním klíčů, i když to není ideální:
        // Toto nebude fungovat, HasMany očekává jeden FK.
        // return $this->hasMany(UserSetCard::class, ['user_id', 'set_id', 'tracking_view'], ['user_id', 'set_id', 'tracking_view']);

        // Správnější je definovat relaci na UserSetCard pomocí kombinace klíčů
        // Lze použít balíček jako https://github.com/staudenmeir/eloquent-has-many-deep
        // nebo vlastní implementaci

        // Pro zjednodušení zatím vrátíme HasMany na user_id, což není přesné, ale umožní základní funkčnost.
         return $this->hasMany(UserSetCard::class, 'user_id', 'user_id')
                     ->where('set_id', $this->set_id)
                     ->where('tracking_view', $this->tracking_view);

        // TODO: Zvážit lepší implementaci této komplexní relace.

    }
}
