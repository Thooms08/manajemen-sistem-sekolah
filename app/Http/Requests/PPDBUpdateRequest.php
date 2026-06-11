<?php

namespace App\Http\Requests;

use App\Models\DataMaster\Murid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi untuk UPDATE/EDIT murid.
 *
 * Strategi:
 * - Semua field menggunakan `sometimes` → hanya divalidasi jika field tersebut
 *   dikirim dalam request. Field yang tidak dikirim tidak divalidasi sama sekali
 *   sehingga tidak menimpa data lama.
 * - Rule::unique()->ignore() berdasarkan ID murid yang diambil via UUID dari route.
 * - Tidak ada field yang `required` — admin bebas mengubah hanya field yang diinginkan.
 */
class PPDBUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Resolve UUID dari route → ambil ID integer murid untuk ignore().
     */
    protected function getMuridId(): ?int
    {
        $uuid = $this->route('uuid');
        if (!$uuid) return null;

        $murid = Murid::where('uuid', $uuid)->first();
        return $murid?->id;
    }

    public function rules(): array
    {
        $id = $this->getMuridId();

        return [
            // ── Data Murid ──────────────────────────────────────────────────
            'nama_lengkap'   => 'sometimes|nullable|string|max:255',
            'jenis_kelamin'  => 'sometimes|nullable|in:laki-laki,perempuan',
            'nisn'           => [
                'sometimes', 'nullable', 'numeric', 'digits:10',
                Rule::unique('murid', 'nisn')->ignore($id),
            ],
            'nik'            => [
                'sometimes', 'nullable', 'numeric', 'digits:16',
                Rule::unique('murid', 'nik')->ignore($id),
            ],
            'nis_baru'       => [
                'sometimes', 'nullable', 'string', 'max:20',
                Rule::unique('murid', 'nis_baru')->ignore($id),
            ],
            'tempat_lahir'   => 'sometimes|nullable|string|max:255',
            'tgl_lahir'      => 'sometimes|nullable|date',
            'rt_rw'          => 'sometimes|nullable|string|max:50',
            'desa_kelurahan' => 'sometimes|nullable|string|max:255',
            'kota_kabupaten' => 'sometimes|nullable|string|max:255',
            'provinsi'       => 'sometimes|nullable|string|max:255',
            'alamat_detail'  => 'sometimes|nullable|string',
            'transportasi'   => 'sometimes|nullable|string|max:255',
            'no_hp'          => 'sometimes|nullable|string|max:20',
            'alamat_email'   => [
                'sometimes', 'nullable', 'email',
                Rule::unique('murid', 'alamat_email')->ignore($id),
            ],
            'sekolah_asal'   => 'sometimes|nullable|string|max:255',
            'tinggi_badan'   => 'sometimes|nullable|numeric|min:0|max:300',
            'berat_badan'    => 'sometimes|nullable|numeric|min:0|max:500',
            'anak_ke'        => 'sometimes|nullable|integer|min:1',
            'jlm_saudara'    => 'sometimes|nullable|integer|min:0',
            'jumlah_kakak'   => 'sometimes|nullable|integer|min:0',
            'jumlah_adik'    => 'sometimes|nullable|integer|min:0',

            // ── Data Orang Tua ──────────────────────────────────────────────
            'nama_ayah'          => 'sometimes|nullable|string|max:255',
            'tempat_lahir_ayah'  => 'sometimes|nullable|string|max:255',
            'tgl_lahir_ayah'     => 'sometimes|nullable|date',
            'pendidikan_ayah'    => 'sometimes|nullable|string|max:255',
            'pekerjaan_ayah'     => 'sometimes|nullable|string|max:255',
            'penghasilan_ayah'   => 'sometimes|nullable|numeric|min:0',
            'status_ayah'        => 'sometimes|nullable|in:hidup,meninggal',
            'nama_ibu'           => 'sometimes|nullable|string|max:255',
            'tempat_lahir_ibu'   => 'sometimes|nullable|string|max:255',
            'tgl_lahir_ibu'      => 'sometimes|nullable|date',
            'pendidikan_ibu'     => 'sometimes|nullable|string|max:255',
            'pekerjaan_ibu'      => 'sometimes|nullable|string|max:255',
            'penghasilan_ibu'    => 'sometimes|nullable|numeric|min:0',
            'status_ibu'         => 'sometimes|nullable|in:hidup,meninggal',

            // ── Data Wali ───────────────────────────────────────────────────
            'nama_wali'          => 'sometimes|nullable|string|max:255',
            'hubungan_wali'      => 'sometimes|nullable|string|max:255',
            'tempat_lahir_wali'  => 'sometimes|nullable|string|max:255',
            'tgl_lahir_wali'     => 'sometimes|nullable|date',
            'pendidikan_wali'    => 'sometimes|nullable|string|max:255',
            'pekerjaan_wali'     => 'sometimes|nullable|string|max:255',
            'penghasilan_wali'   => 'sometimes|nullable|numeric|min:0',
            'status_wali'        => 'sometimes|nullable|in:hidup,meninggal',

            // ── Dokumen (semua opsional saat edit) ──────────────────────────
            'pasfoto'                      => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ktp_ayah'                     => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ktp_ibu'                      => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ktp_wali'                     => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kartu_keluarga'               => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'akte_kelahiran'               => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ijazah_terakhir'              => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'transkip_nilai'               => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_kelulusan'              => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_keterangan_hasil_ujian' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pindahan'               => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'formulir_fisik'               => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.digits'          => 'NISN harus 10 digit',
            'nisn.unique'          => 'NISN sudah digunakan murid lain',
            'nik.digits'           => 'NIK harus 16 digit',
            'nik.unique'           => 'NIK sudah digunakan murid lain',
            'nis_baru.unique'      => 'NIS Baru sudah digunakan murid lain',
            'alamat_email.email'   => 'Format email tidak valid',
            'alamat_email.unique'  => 'Email sudah digunakan murid lain',
        ];
    }
}
