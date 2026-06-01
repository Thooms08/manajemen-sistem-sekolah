<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaMurid extends Model
{
    protected $table = 'biaya_murid';

    protected $fillable = [
        'name',
        'amount',
        'account_id',
        'is_active'
    ];

    public function account()
    {
        return $this->belongsTo(AkunPembayaran::class, 'account_id');
    }
}
