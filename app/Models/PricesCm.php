<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PricesCm
 * 
 * @property string $card_id
 * @property float|null $average_sell_price
 * @property float|null $low_price
 * @property float|null $trend_price
 * @property float|null $german_pro_low
 * @property float|null $suggested_price
 * @property float|null $reverse_holo_sell
 * @property float|null $reverse_holo_low
 * @property float|null $reverse_holo_trend
 * @property float|null $low_price_ex_plus
 * @property float|null $avg1
 * @property float|null $avg7
 * @property float|null $avg30
 * @property float|null $reverse_holo_avg1
 * @property float|null $reverse_holo_avg7
 * @property float|null $reverse_holo_avg30
 * @property Carbon $updated_at
 * @property int $cm_id
 * 
 * @property CardsVariant $cards_variant
 *
 * @package App\Models
 */
class PricesCm extends Model
{
	protected $table = 'prices_cm';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'average_sell_price' => 'float',
		'low_price' => 'float',
		'trend_price' => 'float',
		'german_pro_low' => 'float',
		'suggested_price' => 'float',
		'reverse_holo_sell' => 'float',
		'reverse_holo_low' => 'float',
		'reverse_holo_trend' => 'float',
		'low_price_ex_plus' => 'float',
		'avg1' => 'float',
		'avg7' => 'float',
		'avg30' => 'float',
		'reverse_holo_avg1' => 'float',
		'reverse_holo_avg7' => 'float',
		'reverse_holo_avg30' => 'float',
		'cm_id' => 'int'
	];

	protected $fillable = [
		'card_id',
		'average_sell_price',
		'low_price',
		'trend_price',
		'german_pro_low',
		'suggested_price',
		'reverse_holo_sell',
		'reverse_holo_low',
		'reverse_holo_trend',
		'low_price_ex_plus',
		'avg1',
		'avg7',
		'avg30',
		'reverse_holo_avg1',
		'reverse_holo_avg7',
		'reverse_holo_avg30'
	];

	public function cards_variant()
	{
		return $this->belongsTo(CardsVariant::class, 'cm_id');
	}
}
