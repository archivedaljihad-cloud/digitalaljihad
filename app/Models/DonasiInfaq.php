<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonasiInfaq extends Model
{
    use HasFactory;

    protected $table = 'donasi_infaq';

    protected $fillable = [
        'program_infaq_id',
        'nama_donatur',
        'is_anonim',
        'nominal',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'nominal'    => 'decimal:2',
        'is_anonim'  => 'boolean',
        'tanggal'    => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(ProgramInfaq::class, 'program_infaq_id');
    }

    public function getNamaTampilAttribute()
    {
        if ($this->is_anonim || empty(trim($this->nama_donatur))) {
            return 'Hamba Allah';
        }
        return $this->nama_donatur;
    }
}
