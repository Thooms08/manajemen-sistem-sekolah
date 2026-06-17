<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class ProgramCatatan extends Model
{
    protected $table = 'program_catatan';

    protected $fillable = [
        'id_program',
        'judul',
        'isi',
    ];

    public function program()
    {
        return $this->belongsTo(ProgramSekolah::class, 'id_program');
    }
}
