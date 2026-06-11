<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'judul',
        'deskripsi',
        'teaser',
    ];
}