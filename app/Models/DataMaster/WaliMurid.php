<?php
namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliMurid extends Model
{
    protected $table = 'wali_murid';

    protected $fillable = [
        'id_murid',
        'nama_wali',
        'tempat_lahir_wali',
        'tgl_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'status_wali',
        'hubungan_wali',
    ];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }
}
