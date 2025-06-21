<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Card
 * 
 * @property string $id
 * @property string $set_id
 * @property string $name
 * @property string $supertype
 * @property string|null $number
 * @property string|null $ptcgo_code
 * @property string|null $types
 * @property string|null $subtypes
 * @property string|null $rules
 * @property string|null $rarity
 * @property string|null $regulation_mark
 * @property int|null $hp
 * @property int|null $national_pokedex_number
 * @property string|null $evolves_from
 * @property string|null $evolves_to
 * @property string|null $abilities
 * @property string|null $weaknesses
 * @property string|null $resistances
 * @property string|null $retreat_cost
 * @property int|null $converted_retreat_cost
 * @property string|null $ancient_trait
 * @property string|null $flavor_text
 * @property string|null $illustrator
 * @property string|null $legalities
 * @property string|null $img_small
 * @property string|null $img_file_small
 * @property string|null $img_large
 * @property string|null $img_file_large
 * @property string|null $url_tcgplayer
 * @property string|null $url_cardmarket
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Set $set
 * @property Collection|Attack[] $attacks
 * @property Collection|CardsVariant[] $cards_variants
 * @property Collection|PricesTcg[] $prices_tcgs
 *
 * @package App\Models
 */
class Card extends Model
{
	use HasFactory;

	protected $table = 'cards';
	public $incrementing = false;

	protected $casts = [
		'hp' => 'int',
		'national_pokedex_number' => 'int',
		'converted_retreat_cost' => 'int',
		'types' => 'array',
		'subtypes' => 'array',
		'rules' => 'array',
		'evolves_to' => 'array',
		'abilities' => 'array',
		'weaknesses' => 'array',
		'resistances' => 'array',
		'retreat_cost' => 'array',
		'ancient_trait' => 'array',
		'legalities' => 'array'
	];

	protected $fillable = [
		'set_id',
		'name',
		'supertype',
		'number',
		'ptcgo_code',
		'types',
		'subtypes',
		'rules',
		'rarity',
		'regulation_mark',
		'hp',
		'national_pokedex_number',
		'evolves_from',
		'evolves_to',
		'abilities',
		'weaknesses',
		'resistances',
		'retreat_cost',
		'converted_retreat_cost',
		'ancient_trait',
		'flavor_text',
		'illustrator',
		'legalities',
		'img_small',
		'img_file_small',
		'img_large',
		'img_file_large',
		'url_tcgplayer',
		'url_cardmarket'
	];

	public function set()
	{
		return $this->belongsTo(Set::class);
	}

	public function attacks()
	{
		return $this->hasMany(Attack::class);
	}

	public function cards_variants()
	{
		return $this->hasMany(CardsVariant::class);
	}

	public function prices_tcgs()
	{
		return $this->hasMany(PricesTcg::class);
	}
}
