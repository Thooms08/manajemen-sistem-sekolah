<?php
namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;

class ProfileSekolah extends Model
{
    protected $table = 'profile_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'nis',
        'logo',
        'foto_sekolah',
        'deskripsi',
        'alamat',
        'tautan_google_maps',
        'no_hp',
        'email',
        'akreditasi',
    ];
}