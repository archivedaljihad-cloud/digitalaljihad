<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SholatJumat extends Model
{
    use HasFactory;

    protected $table = 'sholat_jumat';

    protected $fillable = [
        'imam',
        'khatib',
        'muadzin',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public $timestamps = true;

    /**
     * Scope untuk jadwal minggu ini
     */
    public function scopeMingguIni($query)
    {
        return $query->whereBetween('tanggal', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Accessor format tanggal
     */
    public function getFormattedTanggalAttribute()
    {
        return Carbon::parse($this->tanggal)
            ->translatedFormat('l, d F Y');
    }
}