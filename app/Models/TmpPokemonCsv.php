<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TmpPokemonCsv
 * 
 * @property int $cardmarket_id
 * @property string|null $name
 * @property string|null $collector_number
 * @property string|null $rarity
 * @property string|null $expansion
 * @property string|null $expansion_code
 * @property string|null $scryfall_id
 * @property int|null $tcgplayer_id
 *
 * @package App\Models
 */
class TmpPokemonCsv extends Model
{
	protected $table = 'tmp_pokemon_csv';
	protected $primaryKey = 'cardmarket_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'cardmarket_id' => 'int',
		'tcgplayer_id' => 'int'
	];

	protected $fillable = [
		'name',
		'collector_number',
		'rarity',
		'expansion',
		'expansion_code',
		'scryfall_id',
		'tcgplayer_id'
	];
}
