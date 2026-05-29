<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoPpdb extends Model
{
    protected $table = 'info_ppdb';

    protected $fillable = [
        'foto_poster',
        'info_ppdb',
        'ppdb_awal',
        'ppdb_akhir',
    ];
}