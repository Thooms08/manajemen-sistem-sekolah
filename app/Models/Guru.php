<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = [
        'nama_guru',
        'email',
        'no_whatsapp',
        'alamat',
        'status',
        'alasan_nonaktif',
        'surat_keterangan',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_nonaktif' => 'datetime',
    ];

    public function pengajars() {
        return $this->hasMany(Pengajar::class, 'id_guru');
    }

    public function guru() {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}