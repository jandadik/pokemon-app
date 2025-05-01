<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Předpokládáme existenci těchto modelů:
use App\Models\User;
use App\Models\Card;
use App\Models\CardsVariant;

class UserWishlist extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_wishlists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'card_id',
        'variant_id',
        'priority',
        'target_condition',
        'max_price',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_price' => 'decimal:2',
        // Enumy není třeba castovat
    ];

    /**
     * Get the user that owns this wishlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card associated with this wishlist item.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    /**
     * Get the card variant associated with this wishlist item.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(CardsVariant::class, 'variant_id', 'cm_id');
    }
}
