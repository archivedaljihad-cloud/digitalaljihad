<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramInfaq extends Model
{
    use HasFactory;

    protected $table = 'program_infaq';

    protected $fillable = [
        'nama_program',
        'keterangan',
        'target_dana',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'target_dana' => 'decimal:2',
        'is_active'   => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function donasi()
    {
        return $this->hasMany(DonasiInfaq::class, 'program_infaq_id')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
    }

    public function totalTerkumpul()
    {
        return (float) $this->donasi()->sum('nominal');
    }

    public function sisaDana()
    {
        $sisa = (float) $this->target_dana - $this->totalTerkumpul();
        return max(0, $sisa);
    }

    public function persentase()
    {
        if ((float) $this->target_dana <= 0) {
            return 0;
        }
        return min(100, round(($this->totalTerkumpul() / (float) $this->target_dana) * 100, 1));
    }

    public function totalDonatur()
    {
        return $this->donasi()->count();
    }
}
