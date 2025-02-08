<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PricesTcg
 * 
 * @property int $id
 * @property string|null $card_id
 * @property Carbon $updated_at
 * @property string|null $price_type
 * @property float|null $price_low
 * @property float|null $price_mid
 * @property float|null $price_high
 * @property float|null $price_market
 * @property float|null $price_direct_low
 * 
 * @property Card|null $card
 *
 * @package App\Models
 */
class PricesTcg extends Model
{
	protected $table = 'prices_tcg';
	public $timestamps = false;

	protected $casts = [
		'price_low' => 'float',
		'price_mid' => 'float',
		'price_high' => 'float',
		'price_market' => 'float',
		'price_direct_low' => 'float'
	];

	protected $fillable = [
		'card_id',
		'price_type',
		'price_low',
		'price_mid',
		'price_high',
		'price_market',
		'price_direct_low'
	];

	public function card()
	{
		return $this->belongsTo(Card::class);
	}
}
