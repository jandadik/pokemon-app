<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCollection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the collection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the collection.
     */
    public function items(): HasMany
    {
        return $this->hasMany(UserCollectionItem::class, 'collection_id');
    }

    /**
     * Get the price history for the collection.
     */
    public function priceHistory(): HasMany
    {
        return $this->hasMany(UserCollectionPrice::class, 'collection_id');
    }

    /**
     * Get the set tracking associated with this collection (if any).
     */
    public function setTracking(): HasMany
    {
        return $this->hasMany(UserSetTracking::class, 'collection_id');
    }
}
