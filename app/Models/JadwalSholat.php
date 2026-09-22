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

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    public static function getCachedUrutan()
    {
        try {
            return \Illuminate\Support\Facades\Cache::remember('jadwal_sholat_urutkan', 3600, function () {
                return static::urutkan()->get();
            });
        } catch (\Throwable $e) {
            return static::urutkan()->get();
        }
    }

    public static function clearCache(): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('jadwal_sholat_urutkan');
            \Illuminate\Support\Facades\Cache::forget('jadwal_sholat_active');
            \Illuminate\Support\Facades\Cache::forget('prayer_mode_state');
            \Illuminate\Support\Facades\Cache::forget('prayer_mode_state_api');
        } catch (\Throwable $e) {
            // ignore
        }
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