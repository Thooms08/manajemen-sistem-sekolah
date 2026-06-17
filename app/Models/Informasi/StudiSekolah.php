<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class StudiSekolah extends Model
{
    protected $table = 'studi_sekolah';

    protected $fillable = [
        'nama_studi',
        'deskripsi_studi',
    ];

    public function kepalas()
    {
        return $this->hasMany(StudiKepala::class, 'id_studi');
    }

    public function kelass()
    {
        return $this->hasMany(StudiKelas::class, 'id_studi');
    }

    public function catatans()
    {
        return $this->hasMany(StudiCatatan::class, 'id_studi')->latest();
    }
}
