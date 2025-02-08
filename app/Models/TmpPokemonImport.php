<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TmpPokemonImport
 * 
 * @property int $id_product
 * @property string|null $card_id
 * @property string|null $name
 * @property int|null $id_category
 * @property string|null $category_name
 * @property int|null $id_expansion
 * @property int|null $id_metacard
 * @property Carbon|null $date_added
 * @property string|null $namePt
 * @property string|null $collector_number
 * @property string|null $rarity
 * @property string|null $expansion
 * @property string|null $expansion_code
 * @property string|null $scryfall_id
 * @property int|null $tcgplayer_id
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class TmpPokemonImport extends Model
{
	protected $table = 'tmp_pokemon_import';
	protected $primaryKey = 'id_product';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_product' => 'int',
		'id_category' => 'int',
		'id_expansion' => 'int',
		'id_metacard' => 'int',
		'date_added' => 'datetime',
		'tcgplayer_id' => 'int'
	];

	protected $fillable = [
		'card_id',
		'name',
		'id_category',
		'category_name',
		'id_expansion',
		'id_metacard',
		'date_added',
		'namePt',
		'collector_number',
		'rarity',
		'expansion',
		'expansion_code',
		'scryfall_id',
		'tcgplayer_id'
	];
}
