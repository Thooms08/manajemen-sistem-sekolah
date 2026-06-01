<?php

namespace Database\Seeders;

use App\Models\PpdbFormSetting;
use Illuminate\Database\Seeder;

class PpdbFormSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Data Murid fields
        $muridFields = [
            ['field_name' => 'nama_lengkap', 'field_label' => 'Nama Lengkap', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 1],
            ['field_name' => 'jenis_kelamin', 'field_label' => 'Jenis Kelamin', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 2],
            ['field_name' => 'nisn', 'field_label' => 'NISN', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 3],
            ['field_name' => 'nik', 'field_label' => 'NIK', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 4],
            ['field_name' => 'tempat_lahir', 'field_label' => 'Tempat Lahir', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 5],
            ['field_name' => 'tgl_lahir', 'field_label' => 'Tanggal Lahir', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 6],
            ['field_name' => 'rt_rw', 'field_label' => 'RT/RW', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 7],
            ['field_name' => 'desa_kelurahan', 'field_label' => 'Desa/Kelurahan', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 8],
            ['field_name' => 'kota_kabupaten', 'field_label' => 'Kota/Kabupaten', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 9],
            ['field_name' => 'provinsi', 'field_label' => 'Provinsi', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 10],
            ['field_name' => 'alamat_detail', 'field_label' => 'Alamat Detail', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 11],
            ['field_name' => 'transportasi', 'field_label' => 'Transportasi', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 12],
            ['field_name' => 'no_hp', 'field_label' => 'No. HP', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 13],
            ['field_name' => 'alamat_email', 'field_label' => 'Email', 'field_category' => 'murid', 'is_active' => true, 'is_required' => true, 'sort_order' => 14],
            ['field_name' => 'sekolah_asal', 'field_label' => 'Sekolah Asal', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 15],
            ['field_name' => 'tinggi_badan', 'field_label' => 'Tinggi Badan', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 16],
            ['field_name' => 'berat_badan', 'field_label' => 'Berat Badan', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 17],
            ['field_name' => 'anak_ke', 'field_label' => 'Anak Ke', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 18],
            ['field_name' => 'jlm_saudara', 'field_label' => 'Jumlah Saudara', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 19],
            ['field_name' => 'jumlah_kakak', 'field_label' => 'Jumlah Kakak', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 20],
            ['field_name' => 'jumlah_adik', 'field_label' => 'Jumlah Adik', 'field_category' => 'murid', 'is_active' => true, 'is_required' => false, 'sort_order' => 21],
        ];

        // Data Orang Tua fields
        $ortuFields = [
            ['field_name' => 'nama_ayah', 'field_label' => 'Nama Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => true, 'sort_order' => 1],
            ['field_name' => 'tempat_lahir_ayah', 'field_label' => 'Tempat Lahir Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 2],
            ['field_name' => 'tgl_lahir_ayah', 'field_label' => 'Tanggal Lahir Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 3],
            ['field_name' => 'pendidikan_ayah', 'field_label' => 'Pendidikan Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 4],
            ['field_name' => 'pekerjaan_ayah', 'field_label' => 'Pekerjaan Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 5],
            ['field_name' => 'penghasilan_ayah', 'field_label' => 'Penghasilan Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 6],
            ['field_name' => 'status_ayah', 'field_label' => 'Status Ayah', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 7],
            ['field_name' => 'nama_ibu', 'field_label' => 'Nama Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => true, 'sort_order' => 8],
            ['field_name' => 'tempat_lahir_ibu', 'field_label' => 'Tempat Lahir Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 9],
            ['field_name' => 'tgl_lahir_ibu', 'field_label' => 'Tanggal Lahir Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 10],
            ['field_name' => 'pendidikan_ibu', 'field_label' => 'Pendidikan Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 11],
            ['field_name' => 'pekerjaan_ibu', 'field_label' => 'Pekerjaan Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 12],
            ['field_name' => 'penghasilan_ibu', 'field_label' => 'Penghasilan Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 13],
            ['field_name' => 'status_ibu', 'field_label' => 'Status Ibu', 'field_category' => 'ortu', 'is_active' => true, 'is_required' => false, 'sort_order' => 14],
        ];

        // Data Wali fields
        $waliFields = [
            ['field_name' => 'nama_wali', 'field_label' => 'Nama Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 1],
            ['field_name' => 'hubungan_wali', 'field_label' => 'Hubungan Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 2],
            ['field_name' => 'tempat_lahir_wali', 'field_label' => 'Tempat Lahir Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 3],
            ['field_name' => 'tgl_lahir_wali', 'field_label' => 'Tanggal Lahir Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 4],
            ['field_name' => 'pendidikan_wali', 'field_label' => 'Pendidikan Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 5],
            ['field_name' => 'pekerjaan_wali', 'field_label' => 'Pekerjaan Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 6],
            ['field_name' => 'penghasilan_wali', 'field_label' => 'Penghasilan Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 7],
            ['field_name' => 'status_wali', 'field_label' => 'Status Wali', 'field_category' => 'wali', 'is_active' => true, 'is_required' => false, 'sort_order' => 8],
        ];

        // Dokumen fields
        $dokumenFields = [
            ['field_name' => 'ktp_ayah', 'field_label' => 'KTP Ayah', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 1],
            ['field_name' => 'ktp_ibu', 'field_label' => 'KTP Ibu', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 2],
            ['field_name' => 'ktp_wali', 'field_label' => 'KTP Wali', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 3],
            ['field_name' => 'kartu_keluarga', 'field_label' => 'Kartu Keluarga', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 4],
            ['field_name' => 'akte_kelahiran', 'field_label' => 'Akte Kelahiran', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 5],
            ['field_name' => 'ijazah_terakhir', 'field_label' => 'Ijazah Terakhir', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 6],
            ['field_name' => 'transkip_nilai', 'field_label' => 'Transkip Nilai', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 7],
            ['field_name' => 'surat_kelulusan', 'field_label' => 'Surat Kelulusan', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 8],
            ['field_name' => 'surat_keterangan_hasil_ujian', 'field_label' => 'Surat Keterangan Hasil Ujian', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 9],
            ['field_name' => 'surat_pindahan', 'field_label' => 'Surat Pindahan', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 10],
            ['field_name' => 'formulir_fisik', 'field_label' => 'Formulir Fisik', 'field_category' => 'dokumen', 'is_active' => true, 'is_required' => false, 'sort_order' => 11],
        ];

        // Biaya fields (will be dynamic based on BiayaMurid table)
        // These will be managed separately through the BiayaMurid model

        // Insert all fields
        foreach ($muridFields as $field) {
            PpdbFormSetting::updateOrCreate(['field_name' => $field['field_name']], $field);
        }

        foreach ($ortuFields as $field) {
            PpdbFormSetting::updateOrCreate(['field_name' => $field['field_name']], $field);
        }

        foreach ($waliFields as $field) {
            PpdbFormSetting::updateOrCreate(['field_name' => $field['field_name']], $field);
        }

        foreach ($dokumenFields as $field) {
            PpdbFormSetting::updateOrCreate(['field_name' => $field['field_name']], $field);
        }
    }
}
