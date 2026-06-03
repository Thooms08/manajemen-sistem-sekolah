<?php

namespace App\Models\Dokumen;

use App\Models\Murid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPpdb extends Model
{
    protected $connection = 'dokumen_db';
    protected $table      = 'dokumen_ppdb';

    protected $fillable = [
        'id_murid',
        'pasfoto',
        'ktp_ayah',
        'ktp_ibu',
        'ktp_wali',
        'kartu_keluarga',
        'akte_kelahiran',
        'ijazah_terakhir',
        'transkip_nilai',
        'surat_kelulusan',
        'surat_keterangan_hasil_ujian',
        'surat_pindahan',
        'formulir_fisik',
    ];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}
