<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkunPembayaran extends Model
{
    protected $table = 'akun_pembayaran';

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'is_qris',
        'qris_image',
    ];
}
