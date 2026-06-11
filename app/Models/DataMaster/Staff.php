<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'nama_staff',
        'jabatan',
        'email',
        'no_wa',
        'alamat',
        'status',
        'alasan_nonaktif',
        'surat_keterangan',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_nonaktif' => 'datetime',
    ];
}
