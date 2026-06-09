<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class AkunPembayaran extends Model
{
    protected $connection = 'keuangan_db';

    protected $table = 'akun_pembayaran';

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'is_qris',
        'qris_image',
    ];
}
