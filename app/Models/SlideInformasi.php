<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class SlideInformasi extends Model
{
    use HasAutoIncrementId;
    protected $table = 'slide_informasis';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'urutan',
        'durasi',
        'status',
    ];

    /**
     * Accessor URL gambar.
     */
    public function getGambarUrlAttribute()
    {
        return asset('storage/' . $this->gambar);
    }

    /**
     * Scope slide aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope urut berdasarkan urutan.
     */
    public function scopeUrutkan(Builder $query): Builder
    {
        return $query->orderBy('urutan');
    }
}