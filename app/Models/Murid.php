<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Murid extends Model
{
    protected $table = 'murid';
    protected $guarded = ['id']; // Semua kolom bisa diisi kecuali ID

    public function wali(): HasOne
    {
        return $this->hasOne(WaliMurid::class, 'id_murid');
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'murid_kelas', 'id_murid', 'id_kelas');
    }
}