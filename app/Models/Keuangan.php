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

	/**
	 * Scope untuk menyaring transaksi murni Kas Utama Masjid
	 * Memisahkan dan mengecualikan pos penggalangan infaq, kas ambulans, dan program mandiri lainnya.
	 */
	public function scopeKasUtama($query)
	{
		return $query->where(function ($q) {
			$q->whereNull('kategori')
			  ->orWhere('kategori', '')
			  ->orWhere('kategori', 'Kas Utama')
			  ->orWhere('kategori', 'Kas Utama Masjid')
			  ->orWhere('kategori', 'Operasional')
			  ->orWhere(function ($sub) {
				  $sub->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%penggalangan%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%program%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%renovasi%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%ambulance%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%ambulans%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%qurban%'")
					  ->whereRaw("LOWER(COALESCE(kategori, '')) NOT LIKE '%ramadhan%'");
			  });
		});
	}
}