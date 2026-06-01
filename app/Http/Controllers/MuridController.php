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

class MuridController extends Controller
{
    public function index()
    {
        $murids = Murid::where('status', 'konfirmasi')->with(['ortu', 'wali', 'dokumen'])->get();
        return view('dashboard_admin.murid', compact('murids'));
    }

    public function create()
    {
        $murid = null;
        $biayas = BiayaMurid::with('account')->orderBy('id')->get();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $formSettings = \App\Models\PpdbFormSetting::all()->keyBy('field_name');
        return view('dashboard_admin.form_ppdb', compact('murid', 'biayas', 'accounts', 'formSettings'));
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
            return redirect()->route('murid.index')->with('success', 'Murid Baru Berhasil Ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Murid Store Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $murid = Murid::with(['ortu', 'wali', 'dokumen'])->findOrFail($id);
        return response()->json($murid);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $murid = Murid::findOrFail($id);
            
            // Delete related documents
            if ($murid->dokumen) {
                $documentFields = [
                    'ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga', 'akte_kelahiran',
                    'ijazah_terakhir', 'transkip_nilai', 'surat_kelulusan', 
                    'surat_keterangan_hasil_ujian', 'surat_pindahan', 'formulir_fisik'
                ];
                
                foreach ($documentFields as $field) {
                    if ($murid->dokumen->$field) {
                        Storage::delete($murid->dokumen->$field);
                    }
                }
                $murid->dokumen()->delete();
            }
            
            // Delete wali if exists
            if ($murid->wali) {
                $murid->wali()->delete();
            }
            
            // Delete ortu
            if ($murid->ortu) {
                $murid->ortu()->delete();
            }
            
            // Delete murid
            $murid->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Data murid berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Murid Destroy Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function edit($id)
    {
        $murid = Murid::with(['ortu', 'wali', 'dokumen'])->findOrFail($id);
        $biayas = BiayaMurid::with('account')->orderBy('id')->get();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $formSettings = \App\Models\PpdbFormSetting::all()->keyBy('field_name');
        return view('dashboard_admin.form_ppdb', compact('murid', 'biayas', 'accounts', 'formSettings'));
    }

    public function update(PPDBRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $murid = Murid::findOrFail($id);
            
            // 1. Update Data Murid
            $murid->update($request->only([
                'nama_lengkap', 'jenis_kelamin', 'nisn', 'nik', 'tempat_lahir', 'tgl_lahir',
                'rt_rw', 'desa_kelurahan', 'kota_kabupaten', 'provinsi', 'alamat_detail',
                'transportasi', 'no_hp', 'alamat_email', 'sekolah_asal', 'tinggi_badan',
                'berat_badan', 'anak_ke', 'jml_saudara', 'jumlah_kakak', 'jumlah_adik'
            ]));

            // 2. Update Data Orang Tua
            $murid->ortu()->update($request->only([
                'nama_ayah', 'tempat_lahir_ayah', 'tgl_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'status_ayah',
                'nama_ibu', 'tempat_lahir_ibu', 'tgl_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'status_ibu'
            ]));

            // 3. Update Data Wali (jika ada)
            if ($request->filled('nama_wali')) {
                if ($murid->wali) {
                    $murid->wali()->update($request->only([
                        'nama_wali', 'tempat_lahir_wali', 'tgl_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'status_wali', 'hubungan_wali'
                    ]));
                } else {
                    WaliMurid::create(array_merge(
                        $request->only([
                            'nama_wali', 'tempat_lahir_wali', 'tgl_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'status_wali', 'hubungan_wali'
                        ]),
                        ['id_murid' => $murid->id]
                    ));
                }
            } elseif ($murid->wali) {
                $murid->wali()->delete();
            }

            // 4. Update Dokumen (jika ada)
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
                    // Delete old file if exists
                    if ($murid->dokumen && $murid->dokumen->$field) {
                        Storage::delete($murid->dokumen->$field);
                    }
                    $fileName = time() . '_' . $field . '_' . $murid->id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('private/' . $folder, $fileName);
                    $dokumenData[$field] = $filePath;
                }
            }

            if (!empty($dokumenData)) {
                if ($murid->dokumen) {
                    $murid->dokumen()->update($dokumenData);
                } else {
                    $dokumenData['id_murid'] = $murid->id;
                    DokumenMurid::create($dokumenData);
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
        $query = $request->get('query') ?? $request->get('search');
        $kelas_id = $request->get('kelas_id');

        $muridQuery = Murid::query();

        if ($kelas_id) {
            $muridQuery->where('id_kelas', $kelas_id);
        }

        if ($query) {
            $muridQuery->where(function($q) use ($query) {
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
                $output .= '
                <tr>
                    <td class="fw-bold">' . $m->nama_lengkap . '</td>
                    <td>' . $m->nisn . '</td>
                    <td>' . $m->no_hp . '</td>
                    <td class="text-center">
                        <a href="' . route('murid.edit', $m->id) . '" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="' . route('murid.destroy', $m->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Hapus murid ini?\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
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
}