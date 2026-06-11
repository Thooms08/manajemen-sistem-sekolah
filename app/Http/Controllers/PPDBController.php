<?php

namespace App\Http\Controllers;

use App\Http\Requests\PPDBRequest;
use App\Models\Murid;
use App\Models\OrtuMurid;
use App\Models\WaliMurid;
use App\Models\Dokumen\DokumenPpdb;
use App\Models\Keuangan\BiayaMurid;
use App\Models\Keuangan\AkunPembayaran;
use App\Models\Keuangan\BuktiPembayaranPpdb;
use App\Models\PpdbDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PPDBController extends Controller
{
    public function index()
    {
        $isOpen = DB::table('profile_sekolah')->value('is_ppdb_open');

        if (!$isOpen) {
            return view('index.ppdb_tutup');
        }

        // Jalankan 3 query secara bersamaan via Cache agar tidak bolak-balik DB
        $biayas       = Cache::remember('ppdb_biayas', 300, fn() =>
            BiayaMurid::with('account')->orderBy('id')->get()
        );
        $accounts     = Cache::remember('ppdb_accounts', 300, fn() =>
            AkunPembayaran::orderBy('bank_name')->get()
        );
        $formSettings = Cache::remember('ppdb_form_settings', 600, fn() =>
            \App\Models\PpdbFormSetting::all()->keyBy('field_name')
        );

        return view('index.ppdb', compact('biayas', 'accounts', 'formSettings'));
    }

    public function success()
    {
        return view('index.ppdb_berhasil');
    }

    public function checkNISN(Request $request)
    {
        $nisn   = $request->get('nisn');
        $exists = Murid::where('nisn', $nisn)->exists();

        return response()->json([
            'exists'  => $exists,
            'message' => $exists ? 'NISN sudah terdaftar di database' : 'NISN tersedia',
        ]);
    }

    public function checkNIK(Request $request)
    {
        $nik    = $request->get('nik');
        $exists = Murid::where('nik', $nik)->exists();

        return response()->json([
            'exists'  => $exists,
            'message' => $exists ? 'NIK sudah terdaftar di database' : 'NIK tersedia',
        ]);
    }

    public function autoSaveDraft(Request $request)
    {
        $sessionId = $request->session()->getId();

        // Hanya ambil field teks — abaikan file input agar payload tetap kecil
        $draftFields = $request->except([
            '_token', '_method',
            'pasfoto', 'ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga',
            'akte_kelahiran', 'ijazah_terakhir', 'transkip_nilai',
            'surat_kelulusan', 'surat_keterangan_hasil_ujian',
            'surat_pindahan', 'formulir_fisik',
        ]);

        // Gunakan upsert-style manual — lebih ringan dari updateOrCreate di tabel besar
        $exists = DB::table('ppdb_drafts')->where('session_id', $sessionId)->exists();

        $payload = array_merge($draftFields, [
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'updated_at' => now(),
        ]);

        if ($exists) {
            DB::table('ppdb_drafts')->where('session_id', $sessionId)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('ppdb_drafts')->insert($payload);
        }

        return response()->json(['success' => true, 'message' => 'Data tersimpan otomatis']);
    }

    public function getDraftData(Request $request)
    {
        $sessionId = $request->session()->getId();
        $draft     = PpdbDraft::where('session_id', $sessionId)->first();

        if ($draft) {
            return response()->json(['success' => true, 'data' => $draft]);
        }

        return response()->json(['success' => false, 'message' => 'No draft data found']);
    }

    public function store(PPDBRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Data Murid
            $murid = Murid::create([
                'nama_lengkap'   => $request->nama_lengkap,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'nisn'           => $request->nisn,
                'nik'            => $request->nik,
                'nis_lama'       => $request->nis_lama,
                'tempat_lahir'   => $request->tempat_lahir,
                'tgl_lahir'      => $request->tgl_lahir,
                'rt_rw'          => $request->rt_rw,
                'desa_kelurahan' => $request->desa_kelurahan,
                'kota_kabupaten' => $request->kota_kabupaten,
                'provinsi'       => $request->provinsi,
                'alamat_detail'  => $request->alamat_detail,
                'transportasi'   => $request->transportasi,
                'no_hp'          => $request->no_hp,
                'alamat_email'   => $request->alamat_email,
                'sekolah_asal'   => $request->sekolah_asal,
                'tinggi_badan'   => $request->tinggi_badan,
                'berat_badan'    => $request->berat_badan,
                'anak_ke'        => $request->anak_ke,
                'jml_saudara'    => $request->jml_saudara,
                'jumlah_kakak'   => $request->jumlah_kakak,
                'jumlah_adik'    => $request->jumlah_adik,
                'status'         => 'pending',
            ]);

            // 2. Simpan Data Orang Tua
            OrtuMurid::create([
                'id_murid'           => $murid->id,
                'nama_ayah'          => $request->nama_ayah,
                'tempat_lahir_ayah'  => $request->tempat_lahir_ayah,
                'tgl_lahir_ayah'     => $request->tgl_lahir_ayah,
                'pendidikan_ayah'    => $request->pendidikan_ayah,
                'pekerjaan_ayah'     => $request->pekerjaan_ayah,
                'penghasilan_ayah'   => $request->penghasilan_ayah,
                'status_ayah'        => $request->status_ayah,
                'nama_ibu'           => $request->nama_ibu,
                'tempat_lahir_ibu'   => $request->tempat_lahir_ibu,
                'tgl_lahir_ibu'      => $request->tgl_lahir_ibu,
                'pendidikan_ibu'     => $request->pendidikan_ibu,
                'pekerjaan_ibu'      => $request->pekerjaan_ibu,
                'penghasilan_ibu'    => $request->penghasilan_ibu,
                'status_ibu'         => $request->status_ibu,
            ]);

            // 3. Simpan Data Wali (jika ada)
            if ($request->filled('nama_wali')) {
                WaliMurid::create([
                    'id_murid'          => $murid->id,
                    'nama_wali'         => $request->nama_wali,
                    'tempat_lahir_wali' => $request->tempat_lahir_wali,
                    'tgl_lahir_wali'    => $request->tgl_lahir_wali,
                    'pendidikan_wali'   => $request->pendidikan_wali,
                    'pekerjaan_wali'    => $request->pekerjaan_wali,
                    'penghasilan_wali'  => $request->penghasilan_wali,
                    'status_wali'       => $request->status_wali,
                    'hubungan_wali'     => $request->hubungan_wali,
                ]);
            }

            // 4. Simpan Dokumen — kumpulkan semua path dulu, insert sekali
            $documentFields = [
                'pasfoto'                      => 'pasfoto',
                'ktp_ayah'                     => 'ktp-ayah',
                'ktp_ibu'                      => 'ktp-ibu',
                'ktp_wali'                     => 'ktp-wali',
                'kartu_keluarga'               => 'kk',
                'akte_kelahiran'               => 'ak',
                'ijazah_terakhir'              => 'izt',
                'transkip_nilai'               => 'tn',
                'surat_kelulusan'              => 'skl',
                'surat_keterangan_hasil_ujian' => 'skhu',
                'surat_pindahan'               => 'spn',
                'formulir_fisik'               => 'fs',
            ];

            $dokumenData = [];
            // Gunakan Str::uuid() sebagai prefix — lebih unik dari time() dan aman concurrency
            $prefix = Str::uuid()->toString();

            foreach ($documentFields as $field => $folder) {
                if ($request->hasFile($field)) {
                    $file     = $request->file($field);
                    $fileName = $prefix . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $dokumenData[$field] = $file->storeAs('ppdb/' . $folder, $fileName);
                }
            }

            if (!empty($dokumenData)) {
                $dokumenData['id_murid'] = $murid->id;
                DokumenPpdb::create($dokumenData);
            }

            // 5. Simpan Bukti Pembayaran (jika ada — non-cash QRIS/Transfer)
            // Backend tidak mewajibkan agar form admin tidak error
            if ($request->hasFile('bukti_pembayaran')) {
                $biayas = BiayaMurid::with('account')->orderBy('id')->get();

                foreach ($request->file('bukti_pembayaran') as $biayaId => $file) {
                    if ($file && $file->isValid()) {
                        $namaFile = Str::uuid()->toString() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs(
                            'private/bukti_pembayaran_ppdb',
                            $namaFile,
                            'local'
                        );

                        // Cari nama biaya berdasarkan index/id
                        $namaBiaya = null;
                        $biayaData = $biayas->firstWhere('id', $biayaId)
                            ?? $biayas->get((int) $biayaId);
                        if ($biayaData) {
                            $namaBiaya = $biayaData->name;
                        }

                        BuktiPembayaranPpdb::create([
                            'id_murid'   => $murid->id,
                            'nama_biaya' => $namaBiaya,
                            'file_path'  => $path,
                            'file_name'  => $file->getClientOriginalName(),
                            'file_size'  => $file->getSize(),
                        ]);
                    }
                }
            }

            // 6. Hapus draft session setelah berhasil submit (pembersihan async-safe)
            DB::table('ppdb_drafts')
                ->where('session_id', $request->session()->getId())
                ->delete();

            DB::commit();
            return redirect()->route('ppdb.success');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PPDB Store Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
}