<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    // Mengatur nama tabel secara eksplisit
    protected $table = 'mapel';
    protected $fillable = ['nama_mapel', 'deskripsi'];
}