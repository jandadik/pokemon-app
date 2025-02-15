<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CardsVariant
 * 
 * @property int $cm_id
 * @property string|null $card_id
 * @property int|null $cm_expansion_id
 * @property int|null $cm_metacard_id
 * @property Carbon|null $date_added
 * @property string|null $collector_number
 * @property string|null $ptcgo_code
 * @property int|null $tcgplayer_id
 * @property string|null $rarity
 * @property bool $variant_normal
 * @property bool $variant_holo
 * @property bool $variant_reverse
 * @property bool $variant_promo
 * @property int $variant
 * @property bool $variant_pokeball
 * @property bool $variant_masterball
 * 
 * @property Card|null $card
 * @property Collection|PricesCm[] $prices_cms
 *
 * @package App\Models
 */
class CardsVariant extends Model
{
	protected $table = 'cards_variant';
	protected $primaryKey = 'cm_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'cm_id' => 'int',
		'cm_expansion_id' => 'int',
		'cm_metacard_id' => 'int',
		'date_added' => 'datetime',
		'tcgplayer_id' => 'int',
		'variant_normal' => 'bool',
		'variant_holo' => 'bool',
		'variant_reverse' => 'bool',
		'variant_promo' => 'bool',
		'variant' => 'int',
		'variant_pokeball' => 'bool',
		'variant_masterball' => 'bool'
	];

	protected $fillable = [
		'card_id',
		'cm_expansion_id',
		'cm_metacard_id',
		'date_added',
		'collector_number',
		'ptcgo_code',
		'tcgplayer_id',
		'rarity',
		'variant_normal',
		'variant_holo',
		'variant_reverse',
		'variant_promo',
		'variant',
		'variant_pokeball',
		'variant_masterball'
	];

	public function card()
	{
		return $this->belongsTo(Card::class);
	}

	public function prices_cms()
	{
		return $this->hasMany(PricesCm::class, 'cm_id');
	}
}
