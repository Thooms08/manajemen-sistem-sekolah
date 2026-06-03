<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        // Data Murid
        'nama_lengkap',
        'jenis_kelamin',
        'nisn',
        'nik',
        'tempat_lahir',
        'tgl_lahir',
        'rt_rw',
        'desa_kelurahan',
        'kota_kabupaten',
        'provinsi',
        'alamat_detail',
        'transportasi',
        'no_hp',
        'alamat_email',
        'sekolah_asal',
        'tinggi_badan',
        'berat_badan',
        'anak_ke',
        'jlm_saudara',
        'jumlah_kakak',
        'jumlah_adik',
        // Data Orang Tua
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
        // Data Wali
        'nama_wali',
        'hubungan_wali',
        'tempat_lahir_wali',
        'tgl_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'status_wali',
        // Dokumen
        'dokumen_paths',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_lahir_ayah' => 'date',
        'tgl_lahir_ibu' => 'date',
        'tgl_lahir_wali' => 'date',
        'dokumen_paths' => 'array',
    ];
}
