<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $table = 'slides';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'urutan',
        'durasi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'durasi' => 'integer',
        'urutan' => 'integer',
    ];

    public function getGambarUrlAttribute()
    {
        if (empty($this->gambar)) {
            return null;
        }

        return asset('storage/' . $this->gambar);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')
                     ->orderBy('id');
    }
}