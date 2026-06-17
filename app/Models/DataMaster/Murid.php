<?php
namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Models\Dokumen\DokumenPpdb;

class Murid extends Model
{
    protected $table = 'murid';

    protected $fillable = [
        'uuid',
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
        'alasan_nonaktif',
        'alasan_tolak',
        'surat_pernyataan',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_nonaktif' => 'datetime',
    ];

    /**
     * Auto-generate UUID saat create.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

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

    public function kelulusan(): HasOne
    {
       return $this->hasOne(\App\Models\Dokumen\Kelulusan::class, 'id_murid');
    }
}