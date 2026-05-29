<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    protected $table = 'dokumentasi';

    protected $fillable = [
        'foto_kegiatan',
        'label_foto',
        'deskripsi_foto',
    ];
}