<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class Pengumuman extends Model
{
	use HasAutoIncrementId;
	protected $table = 'pengumuman';
	
	protected $fillable = [
		'isi',
		'tanggal',
	];

	public $timestamps = true;
}