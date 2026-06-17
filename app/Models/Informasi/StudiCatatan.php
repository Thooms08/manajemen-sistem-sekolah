<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class StudiCatatan extends Model
{
    protected $table = 'studi_catatan';

    protected $fillable = [
        'id_studi',
        'judul',
        'isi',
    ];

    public function studi()
    {
        return $this->belongsTo(StudiSekolah::class, 'id_studi');
    }
}
