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
}