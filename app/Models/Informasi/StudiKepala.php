<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class StudiKepala extends Model
{
    protected $table = 'studi_kepala';

    protected $fillable = [
        'id_studi',
        'tipe',
        'id_sumber',
        'nama_kepala',
        'jabatan',
    ];

    public function studi()
    {
        return $this->belongsTo(StudiSekolah::class, 'id_studi');
    }
}
