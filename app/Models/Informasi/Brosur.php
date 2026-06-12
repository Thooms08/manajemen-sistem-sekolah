<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class Brosur extends Model
{
    protected $table = 'brosur';

    protected $fillable = [
        'label',
        'path_file',
        'deskripsi',
    ];
}
