<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoArtikel extends Model
{
    protected $table = 'foto_artikel';
    protected $guarded = ['id'];

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'id_artikel');
    }
}