<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artikel extends Model
{
    protected $table = 'artikel';
    // Mengizinkan semua kolom diisi kecuali ID
    protected $guarded = ['id'];

    // Ubah nama fungsi agar seragam dengan panggillan di Controller/View
    public function fotos(): HasMany
    {
        return $this->hasMany(FotoArtikel::class, 'id_artikel');
    }
}