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

    public function detail()
    {
        return $this->hasOne(PrestasiDetail::class, 'id_prestasi');
    }

    public function murids()
    {
        return $this->hasMany(PrestasiMurid::class, 'id_prestasi')->with('murid.kelas');
    }

    public function catatans()
    {
        return $this->hasMany(PrestasiCatatan::class, 'id_prestasi')->latest();
    }
}
