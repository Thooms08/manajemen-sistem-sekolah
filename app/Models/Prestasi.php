<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasi';
    protected $guarded = ['id'];

    // TAMBAHKAN FUNGSI INI
    public function fotos()
    {
        // Parameter kedua adalah foreign key di tabel foto_prestasi
        return $this->hasMany(FotoPrestasi::class, 'id_prestasi');
    }
}