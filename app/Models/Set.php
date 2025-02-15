<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Set
 * 
 * @property string $id
 * @property string $name
 * @property string|null $series
 * @property int|null $printed_total
 * @property int|null $total
 * @property string|null $ptcgo_code
 * @property Carbon|null $release_date
 * @property string|null $symbol_url
 * @property string|null $logo_url
 * 
 * @property Collection|Card[] $cards
 *
 * @package App\Models
 */
class Set extends Model
{
	protected $table = 'sets';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'printed_total' => 'int',
		'total' => 'int',
		'release_date' => 'datetime'
	];

	protected $fillable = [
		'name',
		'series',
		'printed_total',
		'total',
		'ptcgo_code',
		'release_date',
		'symbol_url',
		'logo_url'
	];

	public function cards()
	{
		return $this->hasMany(Card::class);
	}
}
