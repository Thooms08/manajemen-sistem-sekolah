<?php

namespace App\Http\Controllers;

use App\Http\Requests\PPDBStoreRequest;
use App\Http\Requests\PPDBUpdateRequest;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\OrtuMurid;
use App\Models\DataMaster\WaliMurid;
use App\Models\Dokumen\DokumenPpdb;
use App\Models\Keuangan\BiayaMurid;
use App\Models\Keuangan\AkunPembayaran;
use App\Models\PpdbDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MuridController extends Controller
{
    public function index()
    {
        // Tab Aktif: status konfirmasi
        $muridsAktif = Murid::where('status', 'konfirmasi')
            ->with(['ortu', 'wali', 'dokumen'])
            ->latest()
            ->get();

        // Tab Nonaktif: status nonaktif
        $muridsNonaktif = Murid::where('status', 'nonaktif')
            ->with(['ortu', 'wali', 'dokumen'])
            ->latest('tanggal_nonaktif')
            ->get();

        return view('admin.data_master.murid', compact('muridsAktif', 'muridsNonaktif'));
    }

    public function create()
    {
        $murid = null;
        $biayas = BiayaMurid::with('account')->orderBy('id')->get();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $formSettings = \App\Models\PpdbFormSetting::all()->keyBy('field_name');
        return view('admin.ppdb', compact('murid', 'biayas', 'accounts', 'formSettings'));
    }

    public function store(PPDBStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Data Murid
            $murid = Murid::create([
                'nama_lengkap'    => $request->nama_lengkap,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'nisn'            => $request->nisn,
                'nik'             => $request->nik,
                'nis_lama'        => $request->nis_lama,   // store → nis_lama
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

            foreach ($documentFields as $field => $folder) {
                if ($request->hasFile($field)) {
                    $file     = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $murid->id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('ppdb/' . $folder, $fileName);
                    $dokumenData[$field] = $filePath;
                }
            }

            if (!empty($dokumenData)) {
                $dokumenData['id_murid'] = $murid->id;
                DokumenPpdb::create($dokumenData);
            }

            DB::commit();
            return redirect()->route('murid.index')->with('success', 'Murid Baru Berhasil Ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Murid Store Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function show($uuid)
    {
        $murid = Murid::where('uuid', $uuid)->with(['ortu', 'wali', 'dokumen'])->firstOrFail();
        return response()->json($murid);
    }

    /**
     * Kembalikan data lengkap murid untuk modal detail (JSON).
     * Reuse struktur yang sama dengan AdminPPDBController@getDetail.
     */
    public function detail($uuid)
    {
        $murid = Murid::where('uuid', $uuid)->with(['ortu', 'wali', 'dokumen'])->firstOrFail();

        $formSettings = \App\Models\PpdbFormSetting::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('field_category');

        $ortu    = $murid->ortu    ? $murid->ortu->toArray()    : [];
        $wali    = $murid->wali    ? $murid->wali->toArray()    : [];
        $dokumen = $murid->dokumen ? $murid->dokumen->toArray() : [];

        $dokumenUrls = [];
        if ($murid->dokumen) {
            $fileFields = [
                'pasfoto',
                'ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga',
                'akte_kelahiran', 'ijazah_terakhir', 'transkip_nilai',
                'surat_kelulusan', 'surat_keterangan_hasil_ujian',
                'surat_pindahan', 'formulir_fisik',
            ];
            foreach ($fileFields as $field) {
                if (!empty($dokumen[$field])) {
                    $dokumenUrls[$field] = route('murid.dokumen', [
                        'path' => base64_encode($dokumen[$field])
                    ]);
                }
            }
        }

        $settings = [];
        foreach ($formSettings as $category => $fields) {
            foreach ($fields as $field) {
                $settings[$category][] = [
                    'field_name'  => $field->field_name,
                    'field_label' => $field->field_label,
                ];
            }
        }

        return response()->json([
            'murid'        => $murid->toArray(),
            'ortu'         => $ortu,
            'wali'         => $wali,
            'dokumen_urls' => $dokumenUrls,
            'settings'     => $settings,
        ]);
    }

    /**
     * Serve file dokumen privat untuk admin.
     */
    public function serveDokumen(Request $request)
    {
        $path = base64_decode($request->query('path'));

        if (!$path || !Storage::exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::response($path);
    }

    /**
     * Generate & download PDF formulir lengkap murid.
     */
    public function downloadPdf($uuid)
    {
        try {
            $murid   = Murid::where('uuid', $uuid)->with(['ortu', 'wali', 'dokumen', 'kelas'])->firstOrFail();
            $biayas  = BiayaMurid::with('account')->orderBy('id')->get();
            $sekolah = DB::table('profile_sekolah')->first();

            // Daftar dokumen yang sudah diupload
            $dokumenDiupload = [];
            $labelMap = [
                'pasfoto'                      => 'Pasfoto',
                'ktp_ayah'                     => 'KTP Ayah',
                'ktp_ibu'                      => 'KTP Ibu',
                'ktp_wali'                     => 'KTP Wali',
                'kartu_keluarga'               => 'Kartu Keluarga',
                'akte_kelahiran'               => 'Akte Kelahiran',
                'ijazah_terakhir'              => 'Ijazah Terakhir',
                'transkip_nilai'               => 'Transkip Nilai',
                'surat_kelulusan'              => 'Surat Kelulusan',
                'surat_keterangan_hasil_ujian' => 'Surat Keterangan Hasil Ujian',
                'surat_pindahan'               => 'Surat Pindahan',
                'formulir_fisik'               => 'Formulir Fisik',
            ];
            foreach ($labelMap as $field => $label) {
                $dokumenDiupload[$label] = $murid->dokumen && !empty($murid->dokumen->$field);
            }

            // Siapkan path absolut pasfoto untuk DomPDF (butuh path filesystem, bukan URL)
            $pasfotoPath = null;
            if ($murid->dokumen && !empty($murid->dokumen->pasfoto)) {
                $abs = storage_path('app/private/') . ltrim($murid->dokumen->pasfoto, '/');
                if (file_exists($abs)) {
                    $pasfotoPath = $abs;
                }
            }

            // Path absolut logo sekolah
            $logoPath = null;
            if (!empty($sekolah->logo)) {
                $abs = public_path($sekolah->logo);
                if (file_exists($abs)) {
                    $logoPath = $abs;
                }
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('format_file.pdf', compact(
                'murid', 'sekolah', 'biayas', 'dokumenDiupload', 'pasfotoPath', 'logoPath'
            ))->setPaper('a4', 'portrait');

            $namaFile = 'Formulir_PPDB_' . str_replace(' ', '_', $murid->nama_lengkap ?? $id) . '.pdf';

            return $pdf->download($namaFile);

        } catch (\Exception $e) {
            Log::error('PDF Error murid #' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    public function destroy($uuid)
    {
        DB::beginTransaction();
        try {
            $murid = Murid::where('uuid', $uuid)->firstOrFail();

            $alasan = request('alasan_nonaktif');
            $suratPath = null;

            // Simpan file surat pernyataan jika ada
            if (request()->hasFile('surat_pernyataan')) {
                $file = request()->file('surat_pernyataan');
                $fileName = time() . '_surat_' . $murid->id . '.' . $file->getClientOriginalExtension();
                $suratPath = $file->storeAs('nonaktif', $fileName, 'local');
            }

            // Nonaktifkan murid (bukan delete permanen) + tandai status sebagai data mutlak
            $murid->update([
                'status'           => 'nonaktif',
                'alasan_nonaktif'  => $alasan,
                'surat_pernyataan' => $suratPath,
                'tanggal_nonaktif' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Murid berhasil dipindahkan ke data nonaktif.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Murid Nonaktif Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Pulihkan murid nonaktif kembali ke aktif.
     */
    public function restore($uuid)
    {
        $murid = Murid::where('uuid', $uuid)->firstOrFail();

        // Hapus file surat pernyataan jika ada
        if ($murid->surat_pernyataan && Storage::disk('local')->exists($murid->surat_pernyataan)) {
            Storage::disk('local')->delete($murid->surat_pernyataan);
        }

        $murid->update([
            'status'           => 'konfirmasi',
            'alasan_nonaktif'  => null,
            'surat_pernyataan' => null,
            'tanggal_nonaktif' => null,
        ]);

        return redirect()->back()->with('success', 'Murid berhasil dipulihkan ke data aktif.');
    }

    /**
     * Download surat pernyataan nonaktif.
     */
    public function downloadSurat($uuid)
    {
        $murid = Murid::where('uuid', $uuid)->firstOrFail();

        if (!$murid->surat_pernyataan || !Storage::disk('local')->exists($murid->surat_pernyataan)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $murid->surat_pernyataan,
            'Surat_Pernyataan_' . str_replace(' ', '_', $murid->nama_lengkap) . '.' . pathinfo($murid->surat_pernyataan, PATHINFO_EXTENSION)
        );
    }

    public function edit($uuid)
    {
        $murid = Murid::where('uuid', $uuid)->with(['ortu', 'wali', 'dokumen'])->firstOrFail();
        $biayas = BiayaMurid::with('account')->orderBy('id')->get();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $formSettings = \App\Models\PpdbFormSetting::all()->keyBy('field_name');
        return view('admin.ppdb', compact('murid', 'biayas', 'accounts', 'formSettings'));
    }

    public function update(PPDBUpdateRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $murid = Murid::where('uuid', $uuid)->firstOrFail();

            // ── 1. Update Data Murid ─────────────────────────────────────────
            // Hanya update field yang benar-benar dikirim dalam request
            // (input di-disable oleh JS saat step lain aktif, tapi di-enable sebelum submit)
            $muridFields = [
                'nama_lengkap', 'jenis_kelamin', 'nisn', 'nik', 'tempat_lahir', 'tgl_lahir',
                'rt_rw', 'desa_kelurahan', 'kota_kabupaten', 'provinsi', 'alamat_detail',
                'transportasi', 'no_hp', 'alamat_email', 'sekolah_asal', 'tinggi_badan',
                'berat_badan', 'anak_ke', 'jlm_saudara', 'jumlah_kakak', 'jumlah_adik',
                'nis_lama',
            ];

            // Kumpulkan hanya field yang ada dan tidak kosong (biarkan null dari DB)
            $muridData = [];
            foreach ($muridFields as $field) {
                if ($request->has($field)) {
                    $muridData[$field] = $request->input($field);
                }
            }

            // nis_baru: hanya update jika dikirim dan tidak kosong
            if ($request->filled('nis_baru')) {
                $muridData['nis_baru'] = $request->nis_baru;
            }

            if (!empty($muridData)) {
                $murid->update($muridData);
            }

            // ── 2. Update Data Orang Tua ─────────────────────────────────────
            $ortuFields = [
                'nama_ayah', 'tempat_lahir_ayah', 'tgl_lahir_ayah', 'pendidikan_ayah',
                'pekerjaan_ayah', 'penghasilan_ayah', 'status_ayah',
                'nama_ibu', 'tempat_lahir_ibu', 'tgl_lahir_ibu', 'pendidikan_ibu',
                'pekerjaan_ibu', 'penghasilan_ibu', 'status_ibu',
            ];

            $ortuData = [];
            foreach ($ortuFields as $field) {
                if ($request->has($field)) {
                    $ortuData[$field] = $request->input($field);
                }
            }

            if (!empty($ortuData) && $murid->ortu) {
                $murid->ortu()->update($ortuData);
            }

            // ── 3. Update Data Wali ──────────────────────────────────────────
            $waliFields = [
                'nama_wali', 'tempat_lahir_wali', 'tgl_lahir_wali', 'pendidikan_wali',
                'pekerjaan_wali', 'penghasilan_wali', 'status_wali', 'hubungan_wali',
            ];

            $waliData = [];
            foreach ($waliFields as $field) {
                if ($request->has($field)) {
                    $waliData[$field] = $request->input($field);
                }
            }

            if ($request->filled('nama_wali')) {
                // Ada nama wali → simpan/update
                if ($murid->wali) {
                    $murid->wali()->update($waliData);
                } else {
                    WaliMurid::create(array_merge($waliData, ['id_murid' => $murid->id]));
                }
            } elseif ($request->has('nama_wali') && empty($request->nama_wali) && $murid->wali) {
                // Field nama_wali dikirim tapi dikosongkan → hapus data wali
                $murid->wali()->delete();
            }

            // ── 4. Update Dokumen ────────────────────────────────────────────
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
            foreach ($documentFields as $field => $folder) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    if ($murid->dokumen && $murid->dokumen->$field) {
                        Storage::delete($murid->dokumen->$field);
                    }
                    $fileName = time() . '_' . $field . '_' . $murid->id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('ppdb/' . $folder, $fileName);
                    $dokumenData[$field] = $filePath;
                }
            }

            if (!empty($dokumenData)) {
                if ($murid->dokumen) {
                    $murid->dokumen()->update($dokumenData);
                } else {
                    $dokumenData['id_murid'] = $murid->id;
                    DokumenPpdb::create($dokumenData);
                }
            }

            DB::commit();
            return redirect()->route('murid.index')->with('success', 'Data Murid Berhasil Diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Murid Update Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    public function search(Request $request)
    {
        $query    = $request->get('query') ?? $request->get('search');
        $kelas_id = $request->get('kelas_id');
        $tab      = $request->get('tab', 'aktif');

        if ($tab === 'nonaktif') {
            $muridQuery = Murid::where('status', 'nonaktif');
        } else {
            $muridQuery = Murid::where('status', 'konfirmasi');
        }

        if ($kelas_id) {
            $muridQuery->where('id_kelas', $kelas_id);
        }

        if ($query) {
            $muridQuery->where(function ($q) use ($query) {
                $q->where('nama_lengkap', 'LIKE', '%' . $query . '%')
                  ->orWhere('nisn', 'LIKE', '%' . $query . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $query . '%');
            });
        }

        $murids = $muridQuery->get();

        if ($request->ajax() && $request->has('kelas_id')) {
            return response()->json($murids);
        }

        $output = "";
        if ($murids->count() > 0) {
            foreach ($murids as $m) {
                if ($tab === 'nonaktif') {
                    $output .= '
                    <tr>
                        <td class="fw-bold">' . e($m->nama_lengkap) . '</td>
                        <td>' . e($m->nisn) . '</td>
                        <td>' . e($m->no_hp) . '</td>
                        <td><span class="badge bg-secondary">' . e($m->alasan_nonaktif) . '</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-info"
                                    title="Lihat Detail Berkas"
                                    onclick="viewDetail(\'' . $m->uuid . '\')">
                                <i class="bi bi-person-vcard"></i>
                            </button>
                            ' . ($m->surat_pernyataan ? '
                            <a href="' . route('murid.download-surat', $m->uuid) . '" class="btn btn-sm btn-outline-secondary" title="Download Surat">
                                <i class="bi bi-file-earmark-arrow-down"></i>
                            </a>' : '') . '
                            <form action="' . route('murid.restore', $m->uuid) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                <button class="btn btn-sm btn-outline-success" title="Pulihkan" onclick="return confirm(\'Pulihkan murid ini ke data aktif?\')">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </td>
                    </tr>';
                } else {
                    $output .= '
                    <tr>
                        <td class="fw-bold">' . e($m->nama_lengkap) . '</td>
                        <td>' . e($m->nisn) . '</td>
                        <td>' . e($m->no_hp) . '</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-danger"
                                    title="Download PDF Formulir Lengkap"
                                    onclick="downloadPdf(\'' . $m->uuid . '\', \'' . addslashes($m->nama_lengkap) . '\', this)">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-info"
                                    title="Lihat Detail Berkas"
                                    onclick="viewDetail(\'' . $m->uuid . '\')">
                                <i class="bi bi-person-vcard"></i>
                            </button>
                            <a href="' . route('murid.edit', $m->uuid) . '" class="btn btn-sm btn-outline-success" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" title="Nonaktifkan"
                                    onclick="bukaModalHapus(\'' . $m->uuid . '\', \'' . addslashes($m->nama_lengkap) . '\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>';
                }
            }
        } else {
            $cols = ($tab === 'nonaktif') ? 5 : 5;
            $output = '<tr><td colspan="' . $cols . '" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }

    public function getMuridByKelas(Request $request)
    {
        $kelas_id = $request->get('kelas_id');

        if (!$kelas_id) {
            return response()->json([]);
        }

        $murids = DB::table('murid')
            ->join('murid_kelas', 'murid.id', '=', 'murid_kelas.id_murid')
            ->where('murid_kelas.id_kelas', $kelas_id)
            ->select('murid.id', 'murid.nama_lengkap', 'murid.nisn')
            ->orderBy('murid.nama_lengkap', 'asc')
            ->get();

        return response()->json($murids);
    }

    public function checkNISN(Request $request)
    {
        $nisn = $request->get('nisn');
        $excludeId = $request->get('exclude_id');
        
        $query = Murid::where('nisn', $nisn);
        
        // Exclude current murid when editing
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'NISN sudah terdaftar di database' : 'NISN tersedia'
        ]);
    }

    public function checkNIK(Request $request)
    {
        $nik = $request->get('nik');
        $excludeId = $request->get('exclude_id');
        
        $query = Murid::where('nik', $nik);
        
        // Exclude current murid when editing
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'NIK sudah terdaftar di database' : 'NIK tersedia'
        ]);
    }

    public function autoSaveDraft(Request $request)
    {
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();
        
        $draft = PpdbDraft::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ipAddress,
                // Data Murid
                'nama_lengkap' => $request->get('nama_lengkap'),
                'jenis_kelamin' => $request->get('jenis_kelamin'),
                'nisn' => $request->get('nisn'),
                'nik' => $request->get('nik'),
                'tempat_lahir' => $request->get('tempat_lahir'),
                'tgl_lahir' => $request->get('tgl_lahir'),
                'rt_rw' => $request->get('rt_rw'),
                'desa_kelurahan' => $request->get('desa_kelurahan'),
                'kota_kabupaten' => $request->get('kota_kabupaten'),
                'provinsi' => $request->get('provinsi'),
                'alamat_detail' => $request->get('alamat_detail'),
                'transportasi' => $request->get('transportasi'),
                'no_hp' => $request->get('no_hp'),
                'alamat_email' => $request->get('alamat_email'),
                'sekolah_asal' => $request->get('sekolah_asal'),
                'tinggi_badan' => $request->get('tinggi_badan'),
                'berat_badan' => $request->get('berat_badan'),
                'anak_ke' => $request->get('anak_ke'),
                'jlm_saudara' => $request->get('jlm_saudara'),
                'jumlah_kakak' => $request->get('jumlah_kakak'),
                'jumlah_adik' => $request->get('jumlah_adik'),
                // Data Orang Tua
                'nama_ayah' => $request->get('nama_ayah'),
                'tempat_lahir_ayah' => $request->get('tempat_lahir_ayah'),
                'tgl_lahir_ayah' => $request->get('tgl_lahir_ayah'),
                'pendidikan_ayah' => $request->get('pendidikan_ayah'),
                'pekerjaan_ayah' => $request->get('pekerjaan_ayah'),
                'penghasilan_ayah' => $request->get('penghasilan_ayah'),
                'status_ayah' => $request->get('status_ayah'),
                'nama_ibu' => $request->get('nama_ibu'),
                'tempat_lahir_ibu' => $request->get('tempat_lahir_ibu'),
                'tgl_lahir_ibu' => $request->get('tgl_lahir_ibu'),
                'pendidikan_ibu' => $request->get('pendidikan_ibu'),
                'pekerjaan_ibu' => $request->get('pekerjaan_ibu'),
                'penghasilan_ibu' => $request->get('penghasilan_ibu'),
                'status_ibu' => $request->get('status_ibu'),
                // Data Wali
                'nama_wali' => $request->get('nama_wali'),
                'hubungan_wali' => $request->get('hubungan_wali'),
                'tempat_lahir_wali' => $request->get('tempat_lahir_wali'),
                'tgl_lahir_wali' => $request->get('tgl_lahir_wali'),
                'pendidikan_wali' => $request->get('pendidikan_wali'),
                'pekerjaan_wali' => $request->get('pekerjaan_wali'),
                'penghasilan_wali' => $request->get('penghasilan_wali'),
                'status_wali' => $request->get('status_wali'),
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Data tersimpan otomatis'
        ]);
    }

    public function getDraftData(Request $request)
    {
        $sessionId = $request->session()->getId();
        $draft = PpdbDraft::where('session_id', $sessionId)->first();
        
        if ($draft) {
            return response()->json([
                'success' => true,
                'data' => $draft
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No draft data found'
        ]);
    }
}