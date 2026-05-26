<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoPrestasi extends Model
{
    protected $table = 'foto_prestasi';
    protected $guarded = ['id'];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi');
    }
}