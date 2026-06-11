<?php
namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'teaser',
        'foto_artikel',
    ];
}