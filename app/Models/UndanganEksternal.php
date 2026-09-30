<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndanganEksternal extends Model
{
    use HasFactory;

    protected $table = 'undangan_eksternal';

    protected $fillable = [
        'nama_pengundang',
        'nama_acara',
        'penceramah',
        'tanggal_acara',
        'waktu_acara',
        'tempat_acara',
        'keterangan',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];
}
