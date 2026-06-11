<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasi';

    protected $fillable = [
        'foto_prestasi',
        'judul_prestasi',
        'deskripsi_prestasi',
    ];

}