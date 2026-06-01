<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenMurid extends Model
{
    // Note: To use separate dokumen_db connection, add DOKUMEN_DB_CONNECTION=sqlite to .env
    // and uncomment the line below
    // protected $connection = 'dokumen_db';
    protected $table = 'dokumen_murid';

    protected $fillable = [
        'id_murid',
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
