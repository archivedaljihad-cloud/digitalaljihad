<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class KeuanganAmbulance extends Model
{
    use HasAutoIncrementId;

    protected $table = 'keuangan_ambulance';

    protected $fillable = [
        'tanggal',
        'deskripsi',
        'pemasukan',
        'pengeluaran',
        'saldo',
        'kategori',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'pemasukan'   => 'float',
        'pengeluaran' => 'float',
        'saldo'       => 'float',
    ];

    public $timestamps = true;
}
