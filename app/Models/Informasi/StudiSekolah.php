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
}