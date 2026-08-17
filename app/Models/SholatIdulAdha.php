<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SholatIdulAdha extends Model
{
    use HasFactory;

    protected $table = 'sholat_idul_adha';

    protected $fillable = [
        'tahun',
        'tanggal',
        'imam',
        'khatib',
        'muadzin',
        'waktu',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu'   => 'datetime:H:i',
    ];

    /**
     * Scope berdasarkan tahun.
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope tahun berjalan.
     */
    public function scopeTahunIni($query)
    {
        return $query->where('tahun', now()->year);
    }

    /**
     * Accessor tanggal.
     */
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal
            ? $this->tanggal->translatedFormat('l, d F Y')
            : '-';
    }

    /**
     * Accessor waktu.
     */
    public function getFormattedWaktuAttribute()
    {
        return $this->waktu
            ? Carbon::parse($this->waktu)->format('H:i') . ' WIB'
            : '-';
    }
}