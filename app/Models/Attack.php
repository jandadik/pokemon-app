<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Attack
 * 
 * @property int $id
 * @property string $card_id
 * @property string $name
 * @property string|null $cost
 * @property string|null $damage
 * @property string|null $text
 * @property int|null $converted_energy_cost
 * 
 * @property Card $card
 *
 * @package App\Models
 */
class Attack extends Model
{
	protected $table = 'attacks';
	public $timestamps = false;

	protected $casts = [
		'converted_energy_cost' => 'int'
	];

	protected $fillable = [
		'card_id',
		'name',
		'cost',
		'damage',
		'text',
		'converted_energy_cost'
	];

	public function card()
	{
		return $this->belongsTo(Card::class);
	}
}
