<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Kelas;

class StudiKelas extends Model
{
    protected $table = 'studi_kelas';

    protected $fillable = [
        'id_studi',
        'id_kelas',
        'nama_kelas',
    ];

    public function studi()
    {
        return $this->belongsTo(StudiSekolah::class, 'id_studi');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
