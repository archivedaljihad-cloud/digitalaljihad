<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class AgendaKajian extends Model
{
    use HasFactory, HasAutoIncrementId;

    protected $table = 'agenda_kajian';

    protected $fillable = [
        'judul',
        'pemateri',
        'tanggal',
        'waktu',
        'lokasi',
        'deskripsi',
        'gambar',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu'   => 'datetime:H:i',
        'aktif'   => 'boolean',
    ];

    /**
     * Scope data yang aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope pengurutan standar.
     */
    public function scopeUrut($query)
    {
        return $query
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->orderBy('urutan', 'asc');
    }
}