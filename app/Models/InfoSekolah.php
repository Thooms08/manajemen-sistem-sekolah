<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoSekolah extends Model
{
    protected $table = 'info_sekolah';

    protected $fillable = [
        'jumlah_guru',
        'jumlah_staff',
        'nama_kepala_sekolah',
        'foto_kepala_sekolah',
    ];
}