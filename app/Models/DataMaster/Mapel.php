<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    // Mengatur nama tabel secara eksplisit
    protected $table = 'mapel';
    protected $fillable = ['nama_mapel', 'deskripsi'];
}