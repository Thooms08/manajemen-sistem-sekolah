<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class BuktiPembayaranPpdb extends Model
{
    protected $connection = 'keuangan_db';
    protected $table      = 'bukti_pembayaran_ppdb';

    protected $fillable = [
        'id_murid',
        'nama_biaya',
        'file_path',
        'file_name',
        'file_size',
    ];
}
