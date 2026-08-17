<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Nama tabel yang digunakan.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Field yang boleh diisi secara mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Karena tabel settings tidak menggunakan
     * created_at dan updated_at.
     * Ubah menjadi true jika tabel Anda memiliki timestamps.
     *
     * @var bool
     */
    public $timestamps = false;
}