<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAutoIncrementId;

class Slide extends Model
{
    use HasFactory, HasAutoIncrementId;

    protected $table = 'slides';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'urutan',
        'durasi',
        'aktif',
        'gambar_base64',
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

        // 1. Cek apakah file fisik ada di storage/app/public atau public/storage
        $storageFile = storage_path('app/public/' . $this->gambar);
        $publicFile = public_path('storage/' . $this->gambar);
        if ((file_exists($storageFile) && is_file($storageFile)) || (file_exists($publicFile) && is_file($publicFile))) {
            return asset('storage/' . $this->gambar);
        }

        // 2. Jika ada data Base64 tersimpan di database, gunakan langsung
        if (!empty($this->gambar_base64) && str_starts_with($this->gambar_base64, 'data:image/')) {
            return $this->gambar_base64;
        }

        // 3. Fallback cerdas berdasarkan konteks judul/deskripsi jika file fisik terhapus setelah restart server
        $text = strtolower(($this->judul ?? '') . ' ' . ($this->deskripsi ?? ''));
        if (str_contains($text, 'kemenag') || str_contains($text, 'simas')) {
            return asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp');
        }
        if (str_contains($text, 'sertifikat') || str_contains($text, 'berkiblat') || str_contains($text, '1.148') || str_contains($text, '1.448') || str_contains($text, 'rashdul')) {
            return asset('storage/slides/GkxyYVJO2IdZoU1X6mgSNUcghs0gu1HqVtYlxgYA.png');
        }
        if (str_contains($text, 'qiblat') || str_contains($text, 'arah') || str_contains($text, 'kompas')) {
            return asset('storage/slides/1gdpqFYCyv7Sv0qLDTpyxSjMnknbVEM9OLVOjPM3.png');
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