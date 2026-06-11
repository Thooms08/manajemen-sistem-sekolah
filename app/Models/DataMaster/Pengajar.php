<?php

namespace App\Models\DataMaster;
use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    protected $table = 'pengajar';
    protected $fillable = ['id_guru', 'id_mapel', 'id_kelas'];

    public function mapel() {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function kelas() {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function guru() {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}