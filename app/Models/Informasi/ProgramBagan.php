<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class ProgramBagan extends Model
{
    protected $table = 'program_bagan';

    protected $fillable = [
        'id_program',
        'jabatan',
        'tipe_pemegang',
        'id_pemegang',
        'nama_pemegang',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(ProgramSekolah::class, 'id_program');
    }
}
