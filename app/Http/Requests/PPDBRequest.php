<?php
namespace App\Http\Requests;

use App\Models\PpdbFormSetting;
use Illuminate\Foundation\Http\FormRequest;

class PPDBRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $formSettings = PpdbFormSetting::all()->keyBy('field_name');
        $rules = [];

        // Step 1: Data Murid
        if (isset($formSettings['nama_lengkap']) && $formSettings['nama_lengkap']->is_active) {
            $rules['nama_lengkap'] = $formSettings['nama_lengkap']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['jenis_kelamin']) && $formSettings['jenis_kelamin']->is_active) {
            $rules['jenis_kelamin'] = $formSettings['jenis_kelamin']->is_required ? 'required|in:laki-laki,perempuan' : 'nullable|in:laki-laki,perempuan';
        }
        if (isset($formSettings['nisn']) && $formSettings['nisn']->is_active) {
            $rules['nisn'] = $formSettings['nisn']->is_required 
                ? 'required|numeric|digits:10|unique:murid,nisn,' . $this->route('id')
                : 'nullable|numeric|digits:10|unique:murid,nisn,' . $this->route('id');
        }
        if (isset($formSettings['nik']) && $formSettings['nik']->is_active) {
            $rules['nik'] = $formSettings['nik']->is_required 
                ? 'required|numeric|digits:16|unique:murid,nik,' . $this->route('id')
                : 'nullable|numeric|digits:16|unique:murid,nik,' . $this->route('id');
        }
        if (isset($formSettings['tempat_lahir']) && $formSettings['tempat_lahir']->is_active) {
            $rules['tempat_lahir'] = $formSettings['tempat_lahir']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tgl_lahir']) && $formSettings['tgl_lahir']->is_active) {
            $rules['tgl_lahir'] = $formSettings['tgl_lahir']->is_required ? 'required|date' : 'nullable|date';
        }
        if (isset($formSettings['rt_rw']) && $formSettings['rt_rw']->is_active) {
            $rules['rt_rw'] = $formSettings['rt_rw']->is_required ? 'required|string|max:50' : 'nullable|string|max:50';
        }
        if (isset($formSettings['desa_kelurahan']) && $formSettings['desa_kelurahan']->is_active) {
            $rules['desa_kelurahan'] = $formSettings['desa_kelurahan']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['kota_kabupaten']) && $formSettings['kota_kabupaten']->is_active) {
            $rules['kota_kabupaten'] = $formSettings['kota_kabupaten']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['provinsi']) && $formSettings['provinsi']->is_active) {
            $rules['provinsi'] = $formSettings['provinsi']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['alamat_detail']) && $formSettings['alamat_detail']->is_active) {
            $rules['alamat_detail'] = $formSettings['alamat_detail']->is_required ? 'required|string' : 'nullable|string';
        }
        if (isset($formSettings['transportasi']) && $formSettings['transportasi']->is_active) {
            $rules['transportasi'] = $formSettings['transportasi']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['no_hp']) && $formSettings['no_hp']->is_active) {
            $rules['no_hp'] = $formSettings['no_hp']->is_required ? 'required|string|max:20' : 'nullable|string|max:20';
        }
        if (isset($formSettings['alamat_email']) && $formSettings['alamat_email']->is_active) {
            $rules['alamat_email'] = $formSettings['alamat_email']->is_required 
                ? 'required|email|unique:murid,alamat_email,' . $this->route('id')
                : 'nullable|email|unique:murid,alamat_email,' . $this->route('id');
        }
        if (isset($formSettings['sekolah_asal']) && $formSettings['sekolah_asal']->is_active) {
            $rules['sekolah_asal'] = $formSettings['sekolah_asal']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tinggi_badan']) && $formSettings['tinggi_badan']->is_active) {
            $rules['tinggi_badan'] = $formSettings['tinggi_badan']->is_required ? 'required|numeric|min:0|max:300' : 'nullable|numeric|min:0|max:300';
        }
        if (isset($formSettings['berat_badan']) && $formSettings['berat_badan']->is_active) {
            $rules['berat_badan'] = $formSettings['berat_badan']->is_required ? 'required|numeric|min:0|max:500' : 'nullable|numeric|min:0|max:500';
        }
        if (isset($formSettings['anak_ke']) && $formSettings['anak_ke']->is_active) {
            $rules['anak_ke'] = $formSettings['anak_ke']->is_required ? 'required|integer|min:1' : 'nullable|integer|min:1';
        }
        if (isset($formSettings['jlm_saudara']) && $formSettings['jlm_saudara']->is_active) {
            $rules['jlm_saudara'] = $formSettings['jlm_saudara']->is_required ? 'required|integer|min:0' : 'nullable|integer|min:0';
        }
        if (isset($formSettings['jumlah_kakak']) && $formSettings['jumlah_kakak']->is_active) {
            $rules['jumlah_kakak'] = $formSettings['jumlah_kakak']->is_required ? 'required|integer|min:0' : 'nullable|integer|min:0';
        }
        if (isset($formSettings['jumlah_adik']) && $formSettings['jumlah_adik']->is_active) {
            $rules['jumlah_adik'] = $formSettings['jumlah_adik']->is_required ? 'required|integer|min:0' : 'nullable|integer|min:0';
        }

        // Step 2: Data Orang Tua
        if (isset($formSettings['nama_ayah']) && $formSettings['nama_ayah']->is_active) {
            $rules['nama_ayah'] = $formSettings['nama_ayah']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tempat_lahir_ayah']) && $formSettings['tempat_lahir_ayah']->is_active) {
            $rules['tempat_lahir_ayah'] = $formSettings['tempat_lahir_ayah']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tgl_lahir_ayah']) && $formSettings['tgl_lahir_ayah']->is_active) {
            $rules['tgl_lahir_ayah'] = $formSettings['tgl_lahir_ayah']->is_required ? 'required|date' : 'nullable|date';
        }
        if (isset($formSettings['pendidikan_ayah']) && $formSettings['pendidikan_ayah']->is_active) {
            $rules['pendidikan_ayah'] = $formSettings['pendidikan_ayah']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['pekerjaan_ayah']) && $formSettings['pekerjaan_ayah']->is_active) {
            $rules['pekerjaan_ayah'] = $formSettings['pekerjaan_ayah']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['penghasilan_ayah']) && $formSettings['penghasilan_ayah']->is_active) {
            $rules['penghasilan_ayah'] = $formSettings['penghasilan_ayah']->is_required ? 'required|numeric|min:0' : 'nullable|numeric|min:0';
        }
        if (isset($formSettings['status_ayah']) && $formSettings['status_ayah']->is_active) {
            $rules['status_ayah'] = $formSettings['status_ayah']->is_required ? 'required|in:hidup,meninggal' : 'nullable|in:hidup,meninggal';
        }
        if (isset($formSettings['nama_ibu']) && $formSettings['nama_ibu']->is_active) {
            $rules['nama_ibu'] = $formSettings['nama_ibu']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tempat_lahir_ibu']) && $formSettings['tempat_lahir_ibu']->is_active) {
            $rules['tempat_lahir_ibu'] = $formSettings['tempat_lahir_ibu']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tgl_lahir_ibu']) && $formSettings['tgl_lahir_ibu']->is_active) {
            $rules['tgl_lahir_ibu'] = $formSettings['tgl_lahir_ibu']->is_required ? 'required|date' : 'nullable|date';
        }
        if (isset($formSettings['pendidikan_ibu']) && $formSettings['pendidikan_ibu']->is_active) {
            $rules['pendidikan_ibu'] = $formSettings['pendidikan_ibu']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['pekerjaan_ibu']) && $formSettings['pekerjaan_ibu']->is_active) {
            $rules['pekerjaan_ibu'] = $formSettings['pekerjaan_ibu']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['penghasilan_ibu']) && $formSettings['penghasilan_ibu']->is_active) {
            $rules['penghasilan_ibu'] = $formSettings['penghasilan_ibu']->is_required ? 'required|numeric|min:0' : 'nullable|numeric|min:0';
        }
        if (isset($formSettings['status_ibu']) && $formSettings['status_ibu']->is_active) {
            $rules['status_ibu'] = $formSettings['status_ibu']->is_required ? 'required|in:hidup,meninggal' : 'nullable|in:hidup,meninggal';
        }

        // Step 3: Data Wali
        if (isset($formSettings['nama_wali']) && $formSettings['nama_wali']->is_active) {
            $rules['nama_wali'] = $formSettings['nama_wali']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['hubungan_wali']) && $formSettings['hubungan_wali']->is_active) {
            $rules['hubungan_wali'] = $formSettings['hubungan_wali']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tempat_lahir_wali']) && $formSettings['tempat_lahir_wali']->is_active) {
            $rules['tempat_lahir_wali'] = $formSettings['tempat_lahir_wali']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['tgl_lahir_wali']) && $formSettings['tgl_lahir_wali']->is_active) {
            $rules['tgl_lahir_wali'] = $formSettings['tgl_lahir_wali']->is_required ? 'required|date' : 'nullable|date';
        }
        if (isset($formSettings['pendidikan_wali']) && $formSettings['pendidikan_wali']->is_active) {
            $rules['pendidikan_wali'] = $formSettings['pendidikan_wali']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['pekerjaan_wali']) && $formSettings['pekerjaan_wali']->is_active) {
            $rules['pekerjaan_wali'] = $formSettings['pekerjaan_wali']->is_required ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        if (isset($formSettings['penghasilan_wali']) && $formSettings['penghasilan_wali']->is_active) {
            $rules['penghasilan_wali'] = $formSettings['penghasilan_wali']->is_required ? 'required|numeric|min:0' : 'nullable|numeric|min:0';
        }
        if (isset($formSettings['status_wali']) && $formSettings['status_wali']->is_active) {
            $rules['status_wali'] = $formSettings['status_wali']->is_required ? 'required|in:hidup,meninggal' : 'nullable|in:hidup,meninggal';
        }

        // Step 4: Dokumen
        if (isset($formSettings['ktp_ayah']) && $formSettings['ktp_ayah']->is_active) {
            $rules['ktp_ayah'] = $formSettings['ktp_ayah']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['ktp_ibu']) && $formSettings['ktp_ibu']->is_active) {
            $rules['ktp_ibu'] = $formSettings['ktp_ibu']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['ktp_wali']) && $formSettings['ktp_wali']->is_active) {
            $rules['ktp_wali'] = $formSettings['ktp_wali']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['kartu_keluarga']) && $formSettings['kartu_keluarga']->is_active) {
            $rules['kartu_keluarga'] = $formSettings['kartu_keluarga']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['akte_kelahiran']) && $formSettings['akte_kelahiran']->is_active) {
            $rules['akte_kelahiran'] = $formSettings['akte_kelahiran']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['ijazah_terakhir']) && $formSettings['ijazah_terakhir']->is_active) {
            $rules['ijazah_terakhir'] = $formSettings['ijazah_terakhir']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['transkip_nilai']) && $formSettings['transkip_nilai']->is_active) {
            $rules['transkip_nilai'] = $formSettings['transkip_nilai']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['surat_kelulusan']) && $formSettings['surat_kelulusan']->is_active) {
            $rules['surat_kelulusan'] = $formSettings['surat_kelulusan']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['surat_keterangan_hasil_ujian']) && $formSettings['surat_keterangan_hasil_ujian']->is_active) {
            $rules['surat_keterangan_hasil_ujian'] = $formSettings['surat_keterangan_hasil_ujian']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['surat_pindahan']) && $formSettings['surat_pindahan']->is_active) {
            $rules['surat_pindahan'] = $formSettings['surat_pindahan']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }
        if (isset($formSettings['formulir_fisik']) && $formSettings['formulir_fisik']->is_active) {
            $rules['formulir_fisik'] = $formSettings['formulir_fisik']->is_required ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        // Step 5: Biaya Pendaftaran (Payment info)
        $rules['payment_method'] = 'nullable|array';
        $rules['payment_method.*'] = 'required|string|in:qris,transfer,cash';
        $rules['amount_paid'] = 'nullable|array';
        $rules['amount_paid.*'] = 'nullable|numeric|min:0';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'nisn.required' => 'NISN wajib diisi',
            'nisn.digits' => 'NISN harus 10 digit',
            'nik.required' => 'NIK wajib diisi',
            'nik.digits' => 'NIK harus 16 digit',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'alamat_email.required' => 'Email wajib diisi',
            'alamat_email.email' => 'Format email tidak valid',
            'nama_ayah.required' => 'Nama ayah wajib diisi',
            'nama_ibu.required' => 'Nama ibu wajib diisi',
            'status_ayah.required' => 'Status ayah wajib dipilih',
            'status_ibu.required' => 'Status ibu wajib dipilih',
        ];
    }
}
