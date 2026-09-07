<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class Keuangan extends Model
{
	use HasAutoIncrementId;
	protected $table = 'keuangan';
	
	protected $fillable = [
		'tanggal',
		'deskripsi',
		'pemasukan',
		'pengeluaran',
		'saldo',
		'kategori',
	];

	public $timestamps = true;
}