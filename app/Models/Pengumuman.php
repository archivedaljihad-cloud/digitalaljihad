<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class Pengumuman extends Model
{
	use HasAutoIncrementId;
	protected $table = 'pengumuman';
	
	protected $fillable = [
		'judul',
		'pemateri',
		'foto',
		'isi',
		'tanggal',
		'waktu',
		'tempat',
	];

	public function getFotoUrlAttribute()
	{
		if ($this->foto) {
			if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
				return $this->foto;
			}
			return asset('storage/' . $this->foto);
		}
		return null;
	}

	public $timestamps = true;
}