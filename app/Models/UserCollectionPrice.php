<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCollectionPrice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_collection_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_id',
        'total_market_value',
        'total_purchase_value',
        'total_cards',
        'snapshot_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_market_value' => 'decimal:2',
        'total_purchase_value' => 'decimal:2',
        'total_cards' => 'integer',
        'snapshot_date' => 'date',
    ];

    /**
     * Get the collection that this price snapshot belongs to.
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(UserCollection::class, 'collection_id');
    }
}
