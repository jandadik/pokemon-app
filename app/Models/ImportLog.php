<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImportLog
 * 
 * @property int $id
 * @property Carbon $timestamp
 * @property string $operation_type
 * @property string $status
 * @property string|null $item_id
 * @property string|null $item_name
 * @property string|null $message
 * @property string|null $error_details
 *
 * @package App\Models
 */
class ImportLog extends Model
{
	protected $table = 'import_logs';
	public $timestamps = false;

	protected $casts = [
		'timestamp' => 'datetime'
	];

	protected $fillable = [
		'timestamp',
		'operation_type',
		'status',
		'item_id',
		'item_name',
		'message',
		'error_details'
	];
}
