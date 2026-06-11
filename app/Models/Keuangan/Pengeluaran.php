<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $connection = 'keuangan_db';
    protected $table      = 'pengeluaran';

    protected $fillable = [
        'jenis_pengeluaran',   // operasional | gaji_staff | gaji_guru | lainnya
        'keterangan_lainnya',  // isi jika jenis = lainnya
        'nama_pengeluaran',
        'deskripsi',
        'nominal',
        'qty',
        'total',
        'status',              // tersedia | dihapus
        'edited_at',           // timestamp edit terakhir, null jika belum pernah diedit
    ];

    protected $casts = [
        'nominal'   => 'integer',
        'qty'       => 'integer',
        'total'     => 'integer',
        'edited_at' => 'datetime',
    ];

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDihapus($query)
    {
        return $query->where('status', 'dihapus');
    }

    // ── Relasi ─────────────────────────────────────────────────────
    public function buktiPengeluaran()
    {
        return $this->hasMany(BuktiPengeluaran::class, 'id_pengeluaran');
    }
}
