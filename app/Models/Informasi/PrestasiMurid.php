<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Murid;

class PrestasiMurid extends Model
{
    protected $table = 'prestasi_murid';

    protected $fillable = [
        'id_prestasi',
        'id_murid',
        'peran',
    ];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}
