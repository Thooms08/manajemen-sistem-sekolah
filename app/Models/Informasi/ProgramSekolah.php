<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class ProgramSekolah extends Model
{
    protected $table = 'program_sekolah';

    protected $fillable = [
        'nama_program',
        'deskripsi_program',
    ];

    public function pembinas()
    {
        return $this->hasMany(ProgramPembina::class, 'id_program');
    }

    public function anggotas()
    {
        return $this->hasMany(ProgramAnggota::class, 'id_program')->with('murid');
    }

    public function bagans()
    {
        return $this->hasMany(ProgramBagan::class, 'id_program')->orderBy('urutan');
    }

    public function catatans()
    {
        return $this->hasMany(ProgramCatatan::class, 'id_program')->latest();
    }
}
