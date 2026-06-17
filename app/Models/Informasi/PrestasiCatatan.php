<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class PrestasiCatatan extends Model
{
    protected $table = 'prestasi_catatan';

    protected $fillable = [
        'id_prestasi',
        'judul',
        'isi',
    ];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi');
    }
}
