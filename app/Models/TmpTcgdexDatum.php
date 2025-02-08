<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TmpTcgdexDatum
 * 
 * @property string $id
 * @property string|null $image
 * @property bool|null $variant_firstEdition
 * @property bool|null $variant_holo
 * @property bool|null $variant_normal
 * @property bool|null $variant_reverse
 * @property bool|null $variant_wPromo
 * @property Carbon $created_at
 * @property string|null $status
 * @property string|null $id_set
 * @property string|null $id_card
 *
 * @package App\Models
 */
class TmpTcgdexDatum extends Model
{
	protected $table = 'tmp_tcgdex_data';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'variant_firstEdition' => 'bool',
		'variant_holo' => 'bool',
		'variant_normal' => 'bool',
		'variant_reverse' => 'bool',
		'variant_wPromo' => 'bool'
	];

	protected $fillable = [
		'image',
		'variant_firstEdition',
		'variant_holo',
		'variant_normal',
		'variant_reverse',
		'variant_wPromo',
		'status',
		'id_set',
		'id_card'
	];
}
