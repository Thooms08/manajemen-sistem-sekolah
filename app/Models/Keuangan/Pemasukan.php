<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $connection = 'keuangan_db';
    protected $table      = 'pemasukan';

    protected $fillable = [
        'id_murid',
        'jenis_pemasukan',
        'jenis_biaya_ppdb',
        'keterangan_lainnya',
        'keterangan_biaya',
        'nominal',
        'qty',
        'total',
        'edited_at',
        'status',   // 'tersedia' | 'dihapus'
    ];

    protected $casts = [
        'nominal'   => 'integer',
        'qty'       => 'integer',
        'total'     => 'integer',
        'id_murid'  => 'integer',
        'edited_at' => 'datetime',
    ];

    // ── Scope helper ──────────────────────────────────────────────────
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDihapus($query)
    {
        return $query->where('status', 'dihapus');
    }
}
