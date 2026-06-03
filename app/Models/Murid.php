<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Dokumen\DokumenPpdb;

class Murid extends Model
{
    protected $table = 'murid';

    protected $fillable = [
        'nama_lengkap',
        'jenis_kelamin',
        'nisn',
        'nik',
        'nis_lama',
        'nis_baru',
        'tempat_lahir',
        'tgl_lahir',
        'rt_rw',
        'desa_kelurahan',
        'kota_kabupaten',
        'provinsi',
        'alamat_detail',
        'transportasi',
        'no_hp',
        'alamat_email',
        'sekolah_asal',
        'tinggi_badan',
        'berat_badan',
        'anak_ke',
        'jlm_saudara',
        'jumlah_kakak',
        'jumlah_adik',
        'status',
    ];

    public function ortu(): HasOne
    {
        return $this->hasOne(OrtuMurid::class, 'id_murid');
    }

    public function wali(): HasOne
    {
        return $this->hasOne(WaliMurid::class, 'id_murid');
    }

    public function dokumen(): HasOne
    {
        return $this->hasOne(DokumenPpdb::class, 'id_murid');
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'murid_kelas', 'id_murid', 'id_kelas');
    }
}