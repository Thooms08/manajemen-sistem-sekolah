<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class BiayaMurid extends Model
{
    protected $connection = 'keuangan_db';

    protected $table = 'biaya_murid';

    protected $fillable = [
        'name',
        'amount',
        'account_id',
        'is_active',
        'disabled_reason',
    ];

    public function account()
    {
        return $this->belongsTo(AkunPembayaran::class, 'account_id');
    }
}
