<?php

namespace App\Http\Controllers;

use App\Http\Requests\PPDBRequest;
use App\Models\Murid;
use App\Models\OrtuMurid;
use App\Models\WaliMurid;
use App\Models\DokumenMurid;
use App\Models\BiayaMurid;
use App\Models\AkunPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PPDBController extends Controller
{
    public function index()
    {
        $isOpen = DB::table('profile_sekolah')->value('is_ppdb_open');

        if (!$isOpen) {
            return view('index.ppdb_tutup');
        }

        // Get biaya data for payment display
        $biayas = BiayaMurid::with('account')->orderBy('id')->get();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $formSettings = \App\Models\PpdbFormSetting::all()->keyBy('field_name');

        return view('index.ppdb', compact('biayas', 'accounts', 'formSettings'));
    }

    public function success()
    {
        return view('index.ppdb_berhasil');
    }

    public function checkNISN(Request $request)
    {
        $nisn = $request->get('nisn');
        $exists = Murid::where('nisn', $nisn)->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'NISN sudah terdaftar di database' : 'NISN tersedia'
        ]);
    }

    public function store(PPDBRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Data Murid
            $murid = Murid::create([
                'nama_lengkap'    => $request->nama_lengkap,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'nisn'            => $request->nisn,
                'nik'             => $request->nik,
                'tempat_lahir'    => $request->tempat_lahir,
                'tgl_lahir'       => $request->tgl_lahir,
                'rt_rw'           => $request->rt_rw,
                'desa_kelurahan'  => $request->desa_kelurahan,
                'kota_kabupaten'  => $request->kota_kabupaten,
                'provinsi'        => $request->provinsi,
                'alamat_detail'   => $request->alamat_detail,
                'transportasi'    => $request->transportasi,
                'no_hp'           => $request->no_hp,
                'alamat_email'    => $request->alamat_email,
                'sekolah_asal'    => $request->sekolah_asal,
                'tinggi_badan'    => $request->tinggi_badan,
                'berat_badan'     => $request->berat_badan,
                'anak_ke'         => $request->anak_ke,
                'jml_saudara'     => $request->jml_saudara,
                'jumlah_kakak'    => $request->jumlah_kakak,
                'jumlah_adik'     => $request->jumlah_adik,
                'status'          => 'pending',
            ]);

            // 2. Simpan Data Orang Tua
            OrtuMurid::create([
                'id_murid'            => $murid->id,
                'nama_ayah'           => $request->nama_ayah,
                'tempat_lahir_ayah'   => $request->tempat_lahir_ayah,
                'tgl_lahir_ayah'      => $request->tgl_lahir_ayah,
                'pendidikan_ayah'     => $request->pendidikan_ayah,
                'pekerjaan_ayah'      => $request->pekerjaan_ayah,
                'penghasilan_ayah'    => $request->penghasilan_ayah,
                'status_ayah'         => $request->status_ayah,
                'nama_ibu'            => $request->nama_ibu,
                'tempat_lahir_ibu'    => $request->tempat_lahir_ibu,
                'tgl_lahir_ibu'       => $request->tgl_lahir_ibu,
                'pendidikan_ibu'      => $request->pendidikan_ibu,
                'pekerjaan_ibu'       => $request->pekerjaan_ibu,
                'penghasilan_ibu'     => $request->penghasilan_ibu,
                'status_ibu'          => $request->status_ibu,
            ]);

            // 3. Simpan Data Wali (jika ada)
            if ($request->filled('nama_wali')) {
                WaliMurid::create([
                    'id_murid'            => $murid->id,
                    'nama_wali'           => $request->nama_wali,
                    'tempat_lahir_wali'   => $request->tempat_lahir_wali,
                    'tgl_lahir_wali'      => $request->tgl_lahir_wali,
                    'pendidikan_wali'     => $request->pendidikan_wali,
                    'pekerjaan_wali'      => $request->pekerjaan_wali,
                    'penghasilan_wali'    => $request->penghasilan_wali,
                    'status_wali'         => $request->status_wali,
                    'hubungan_wali'      => $request->hubungan_wali,
                ]);
            }

            // 4. Simpan Dokumen (jika ada)
            $dokumenData = [];
            $documentFields = [
                'ktp_ayah' => 'ktp-ayah',
                'ktp_ibu' => 'ktp-ibu',
                'ktp_wali' => 'ktp-wali',
                'kartu_keluarga' => 'kk',
                'akte_kelahiran' => 'ak',
                'ijazah_terakhir' => 'izt',
                'transkip_nilai' => 'tn',
                'surat_kelulusan' => 'skl',
                'surat_keterangan_hasil_ujian' => 'skhu',
                'surat_pindahan' => 'spn',
                'formulir_fisik' => 'fs',
            ];

            foreach ($documentFields as $field => $folder) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $murid->id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('private/' . $folder, $fileName);
                    $dokumenData[$field] = $filePath;
                }
            }

            if (!empty($dokumenData)) {
                $dokumenData['id_murid'] = $murid->id;
                DokumenMurid::create($dokumenData);
            }

            DB::commit();
            return redirect()->route('ppdb.success');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PPDB Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
}