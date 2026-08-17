<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JadwalSholat extends Model
{
    protected $table = 'jadwal_sholat';

    protected $fillable = [
        'nama_sholat',
        'waktu',
    ];

    public $timestamps = true;

    /**
     * Scope untuk mengurutkan jadwal sholat sesuai urutan harian.
     */
    public function scopeUrutkan(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE nama_sholat
                WHEN 'Subuh' THEN 1
                WHEN 'Dzuhur' THEN 2
                WHEN 'Ashar' THEN 3
                WHEN 'Maghrib' THEN 4
                WHEN 'Isya' THEN 5
                ELSE 99
            END
        ");
    }
}