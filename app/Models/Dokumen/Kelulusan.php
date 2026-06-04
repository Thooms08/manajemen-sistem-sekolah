<?php

namespace App\Models\Dokumen;

use App\Models\Murid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Kelulusan extends Model
{
    // Menggunakan koneksi database dokumen_db
    protected $connection = 'dokumen_db';
    
    protected $table = 'kelulusan';

    protected $fillable = [
        'uuid',
        'id_murid',
        'status',
        'tahun_lulus',
        'ijazah',
        'raport',
        'surat_kelulusan',
    ];

    /**
     * Auto-generate UUID saat create (via Model Event)
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

    /**
     * Hubungan balik ke model Murid (lintas database)
     */
    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}