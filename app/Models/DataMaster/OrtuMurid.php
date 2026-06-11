<?php
namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrtuMurid extends Model
{
    protected $table = 'ortu_murid';

    protected $fillable = [
        'id_murid',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tgl_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'status_ayah',
        'nama_ibu',
        'tempat_lahir_ibu',
        'tgl_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'status_ibu',
    ];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}