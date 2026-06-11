<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class BuktiPengeluaran extends Model
{
    protected $connection = 'keuangan_db';
    protected $table      = 'bukti_pengeluaran';

    protected $fillable = [
        'id_pengeluaran',
        'bukti_foto',      // path relatif dari disk 'local' (storage/app/bukti_pengeluaran/...)
    ];

    // ── Relasi ─────────────────────────────────────────────────────
    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }
}
