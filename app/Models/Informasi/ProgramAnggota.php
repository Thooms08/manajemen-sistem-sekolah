<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Murid;

class ProgramAnggota extends Model
{
    protected $table = 'program_anggota';

    protected $fillable = [
        'id_program',
        'id_murid',
    ];

    public function program()
    {
        return $this->belongsTo(ProgramSekolah::class, 'id_program');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}
