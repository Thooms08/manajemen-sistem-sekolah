<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class PrestasiDetail extends Model
{
    protected $table = 'prestasi_detail';

    protected $fillable = [
        'id_prestasi',
        'jenis',
        'bidang',
        'tingkat',
        'peringkat',
        'penyelenggara',
        'tanggal_pelaksanaan',
        'lokasi',
        'nama_tim',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'id_prestasi');
    }
}
