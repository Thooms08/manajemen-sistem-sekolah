<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;

class ProgramPembina extends Model
{
    protected $table = 'program_pembina';

    protected $fillable = [
        'id_program',
        'tipe',
        'id_sumber',
        'peran',
    ];

    public function program()
    {
        return $this->belongsTo(ProgramSekolah::class, 'id_program');
    }

    /**
     * Ambil nama pembina secara dinamis berdasarkan tipe.
     */
    public function getNamaAttribute(): string
    {
        if ($this->tipe === 'guru') {
            $guru = Guru::find($this->id_sumber);
            return $guru ? $guru->nama_guru : '(Guru dihapus)';
        }
        $staff = Staff::find($this->id_sumber);
        return $staff ? $staff->nama_staff : '(Staff dihapus)';
    }
}
