<?php
namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'id_wali_kelas'];

    public function murid(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_kelas', 'id_kelas', 'id_murid');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_wali_kelas');
    }

    public function pengajars()
    {
        return $this->hasMany(Pengajar::class, 'id_kelas');
    }
}