<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = ['id_user', 'nama_guru', 'mapel', 'email', 'no_whatsapp', 'alamat'];
}