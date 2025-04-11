<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
	
	/**
	 * Získá celkovou tržní cenu setu (součet avg30 a reverse_holo_avg30)
	 */
	public function getMarketPrice()
	{
		// Použijeme přímý přístup k tabulce prices_cm
		$totalPrice = 0;
		
		// Nejdříve získáme nejnovější updated_at pro každou kartu
		$latestCardIds = DB::table('prices_cm')
			->where('card_id', 'like', $this->id . '-%')
			->select('card_id', DB::raw('MAX(updated_at) as latest_update'))
			->groupBy('card_id');
		
		// ID setu se používá jako prefix v card_id (např. set-123 pro karty setu s ID 'set')
		// Nyní získáme pouze nejnovější záznamy pro každou kartu
		$priceResults = DB::table('prices_cm as p1')
			->joinSub($latestCardIds, 'latest', function($join) {
				$join->on('p1.card_id', '=', 'latest.card_id')
					 ->on('p1.updated_at', '=', 'latest.latest_update');
			})
			->select(
				DB::raw('SUM(COALESCE(p1.avg30, 0)) as total_avg30'),
				DB::raw('SUM(COALESCE(p1.reverse_holo_avg30, 0)) as total_reverse_holo_avg30')
			)
			->first();
		
		if ($priceResults) {
			$totalPrice = ($priceResults->total_avg30 ?? 0) + ($priceResults->total_reverse_holo_avg30 ?? 0);
		}
		
		// Vymazání cache pro tento set
		\Illuminate\Support\Facades\Cache::forget('set_market_price_' . $this->id);
		
		return round($totalPrice, 2);
	}
}
