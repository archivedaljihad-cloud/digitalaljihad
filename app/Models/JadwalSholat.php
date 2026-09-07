<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JadwalSholat extends Model
{
    protected $table = 'jadwal_sholat';

    protected $fillable = [
        'id',
        'nama_sholat',
        'waktu',
        'durasi_jeda',
    ];

    public $timestamps = true;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $maxId = static::max('id') ?? 0;
                $model->id = $maxId + 1;
            }
        });
    }

    /**
     * Scope untuk mengurutkan jadwal sholat sesuai urutan harian.
     */
    public function scopeUrutkan(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE nama_sholat
                WHEN 'Imsak' THEN 1
                WHEN 'Subuh' THEN 2
                WHEN 'Syuruk' THEN 3
                WHEN 'Dzuhur' THEN 4
                WHEN 'Ashar' THEN 5
                WHEN 'Maghrib' THEN 6
                WHEN 'Isya' THEN 7
                ELSE 99
            END
        ");
    }
}