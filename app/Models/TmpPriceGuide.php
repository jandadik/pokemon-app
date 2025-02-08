<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TmpPriceGuide
 * 
 * @property int $id_product
 * @property int|null $id_category
 * @property float|null $avg
 * @property float|null $low
 * @property float|null $trend
 * @property float|null $avg1
 * @property float|null $avg7
 * @property float|null $avg30
 * @property float|null $avg_holo
 * @property float|null $low_holo
 * @property float|null $trend_holo
 * @property float|null $avg1_holo
 * @property float|null $avg7_holo
 * @property float|null $avg30_holo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class TmpPriceGuide extends Model
{
	protected $table = 'tmp_price_guide';
	public $incrementing = false;

	protected $casts = [
		'id_product' => 'int',
		'id_category' => 'int',
		'avg' => 'float',
		'low' => 'float',
		'trend' => 'float',
		'avg1' => 'float',
		'avg7' => 'float',
		'avg30' => 'float',
		'avg_holo' => 'float',
		'low_holo' => 'float',
		'trend_holo' => 'float',
		'avg1_holo' => 'float',
		'avg7_holo' => 'float',
		'avg30_holo' => 'float'
	];

	protected $fillable = [
		'id_category',
		'avg',
		'low',
		'trend',
		'avg1',
		'avg7',
		'avg30',
		'avg_holo',
		'low_holo',
		'trend_holo',
		'avg1_holo',
		'avg7_holo',
		'avg30_holo'
	];
}
