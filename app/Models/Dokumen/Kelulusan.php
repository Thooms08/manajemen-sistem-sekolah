<?php

namespace App\Models\Dokumen;

use App\Models\Murid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kelulusan extends Model
{
    // Menggunakan koneksi database dokumen_db
    protected $connection = 'dokumen_db';
    
    protected $table = 'kelulusan';

    protected $fillable = [
        'id_murid',
        'status',
        'tahun_lulus',
        'ijazah',
        'raport',
        'surat_kelulusan',
    ];

    /**
     * Hubungan balik ke model Murid (lintas database)
     */
    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}