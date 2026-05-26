<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliMurid extends Model
{
    protected $table = 'wali_murid';
    protected $guarded = ['id'];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}