<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas'];

    public function murid(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_kelas', 'id_kelas', 'id_murid');
    }
}