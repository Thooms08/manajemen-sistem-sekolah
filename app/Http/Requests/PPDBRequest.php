<?php
namespace App\Http\Requests;

use App\Models\Pengaturan\PpdbFormSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class PPDBRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Cache form settings selama 10 menit — hindari query DB ulang setiap request submit
        $formSettings = Cache::remember('ppdb_form_settings', 600, function () {
            return PpdbFormSetting::all()->keyBy('field_name');
        });

        $rules = [];
        $routeId = $this->route('id');

        // Helper closure untuk mengurangi duplikasi logika is_required
        $addRule = function (string $field, string $requiredRule, string $nullableRule) use (&$rules, $formSettings) {
            if (isset($formSettings[$field]) && $formSettings[$field]->is_active) {
                $rules[$field] = $formSettings[$field]->is_required ? $requiredRule : $nullableRule;
            }
        };

        $addRuleArr = function (string $field, array $requiredRule, array $nullableRule) use (&$rules, $formSettings) {
            if (isset($formSettings[$field]) && $formSettings[$field]->is_active) {
                $rules[$field] = $formSettings[$field]->is_required ? $requiredRule : $nullableRule;
            }
        };

        // ── Step 1: Data Murid ──────────────────────────────────────────────
        $addRule('nama_lengkap',    'required|string|max:255',      'nullable|string|max:255');
        $addRule('jenis_kelamin',   'required|in:laki-laki,perempuan', 'nullable|in:laki-laki,perempuan');
        $addRule('tempat_lahir',    'required|string|max:255',      'nullable|string|max:255');
        $addRule('tgl_lahir',       'required|date',                'nullable|date');
        $addRule('rt_rw',           'required|string|max:50',       'nullable|string|max:50');
        $addRule('desa_kelurahan',  'required|string|max:255',      'nullable|string|max:255');
        $addRule('kota_kabupaten',  'required|string|max:255',      'nullable|string|max:255');
        $addRule('provinsi',        'required|string|max:255',      'nullable|string|max:255');
        $addRule('alamat_detail',   'required|string',              'nullable|string');
        $addRule('transportasi',    'required|string|max:255',      'nullable|string|max:255');
        $addRule('no_hp',           'required|string|max:20',       'nullable|string|max:20');
        $addRule('sekolah_asal',    'required|string|max:255',      'nullable|string|max:255');
        $addRule('tinggi_badan',    'required|numeric|min:0|max:300', 'nullable|numeric|min:0|max:300');
        $addRule('berat_badan',     'required|numeric|min:0|max:500', 'nullable|numeric|min:0|max:500');
        $addRule('anak_ke',         'required|integer|min:1',       'nullable|integer|min:1');
        $addRule('jlm_saudara',     'required|integer|min:0',       'nullable|integer|min:0');
        $addRule('jumlah_kakak',    'required|integer|min:0',       'nullable|integer|min:0');
        $addRule('jumlah_adik',     'required|integer|min:0',       'nullable|integer|min:0');

        // Unique fields (butuh Rule::unique)
        $addRuleArr('nisn', [
            'required', 'numeric', 'digits:10',
            Rule::unique('murid', 'nisn')->ignore($routeId),
        ], [
            'nullable', 'numeric', 'digits:10',
            Rule::unique('murid', 'nisn')->ignore($routeId),
        ]);

        $addRuleArr('nik', [
            'required', 'numeric', 'digits:16',
            Rule::unique('murid', 'nik')->ignore($routeId),
        ], [
            'nullable', 'numeric', 'digits:16',
            Rule::unique('murid', 'nik')->ignore($routeId),
        ]);

        $addRuleArr('alamat_email', [
            'required', 'email',
            Rule::unique('murid', 'alamat_email')->ignore($routeId),
        ], [
            'nullable', 'email',
            Rule::unique('murid', 'alamat_email')->ignore($routeId),
        ]);

        // ── Step 2: Data Orang Tua ──────────────────────────────────────────
        $addRule('nama_ayah',          'required|string|max:255',    'nullable|string|max:255');
        $addRule('tempat_lahir_ayah',  'required|string|max:255',    'nullable|string|max:255');
        $addRule('tgl_lahir_ayah',     'required|date',              'nullable|date');
        $addRule('pendidikan_ayah',    'required|string|max:255',    'nullable|string|max:255');
        $addRule('pekerjaan_ayah',     'required|string|max:255',    'nullable|string|max:255');
        $addRule('penghasilan_ayah',   'required|numeric|min:0',     'nullable|numeric|min:0');
        $addRule('status_ayah',        'required|in:hidup,meninggal', 'nullable|in:hidup,meninggal');
        $addRule('nama_ibu',           'required|string|max:255',    'nullable|string|max:255');
        $addRule('tempat_lahir_ibu',   'required|string|max:255',    'nullable|string|max:255');
        $addRule('tgl_lahir_ibu',      'required|date',              'nullable|date');
        $addRule('pendidikan_ibu',     'required|string|max:255',    'nullable|string|max:255');
        $addRule('pekerjaan_ibu',      'required|string|max:255',    'nullable|string|max:255');
        $addRule('penghasilan_ibu',    'required|numeric|min:0',     'nullable|numeric|min:0');
        $addRule('status_ibu',         'required|in:hidup,meninggal', 'nullable|in:hidup,meninggal');

        // ── Step 3: Data Wali ───────────────────────────────────────────────
        $addRule('nama_wali',          'required|string|max:255',    'nullable|string|max:255');
        $addRule('hubungan_wali',      'required|string|max:255',    'nullable|string|max:255');
        $addRule('tempat_lahir_wali',  'required|string|max:255',    'nullable|string|max:255');
        $addRule('tgl_lahir_wali',     'required|date',              'nullable|date');
        $addRule('pendidikan_wali',    'required|string|max:255',    'nullable|string|max:255');
        $addRule('pekerjaan_wali',     'required|string|max:255',    'nullable|string|max:255');
        $addRule('penghasilan_wali',   'required|numeric|min:0',     'nullable|numeric|min:0');
        $addRule('status_wali',        'required|in:hidup,meninggal', 'nullable|in:hidup,meninggal');

        // ── Step 4: Dokumen ─────────────────────────────────────────────────
        $docFields = [
            'ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga',
            'akte_kelahiran', 'ijazah_terakhir', 'transkip_nilai',
            'surat_kelulusan', 'surat_keterangan_hasil_ujian',
            'surat_pindahan', 'formulir_fisik',
        ];
        foreach ($docFields as $field) {
            $addRule($field,
                'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
            );
        }
        // pasfoto tidak ada di $docFields umum — tetap individual
        if (isset($formSettings['pasfoto']) && $formSettings['pasfoto']->is_active) {
            $rules['pasfoto'] = $formSettings['pasfoto']->is_required
                ? 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
                : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        // ── Step 5: Biaya Pendaftaran ───────────────────────────────────────
        $rules['payment_method']   = 'nullable|array';
        $rules['payment_method.*'] = 'required|string|in:qris,transfer,cash';
        $rules['amount_paid']      = 'nullable|array';
        $rules['amount_paid.*']    = 'nullable|numeric|min:0';

        // Bukti pembayaran PPDB — nullable dari backend agar tidak error
        // (frontend sudah handle required jika metode non-cash)
        $rules['bukti_pembayaran']   = 'nullable|array';
        $rules['bukti_pembayaran.*'] = 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'nisn.required'          => 'NISN wajib diisi',
            'nisn.digits'            => 'NISN harus 10 digit',
            'nik.required'           => 'NIK wajib diisi',
            'nik.digits'             => 'NIK harus 16 digit',
            'no_hp.required'         => 'Nomor HP wajib diisi',
            'alamat_email.required'  => 'Email wajib diisi',
            'alamat_email.email'     => 'Format email tidak valid',
            'nama_ayah.required'     => 'Nama ayah wajib diisi',
            'nama_ibu.required'      => 'Nama ibu wajib diisi',
            'status_ayah.required'   => 'Status ayah wajib dipilih',
            'status_ibu.required'    => 'Status ibu wajib dipilih',
        ];
    }
}
