<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\Dokumen\Kelulusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelulusanController extends Controller
{
    /**
     * Tampilan Utama Halaman Kelulusan
     */
    public function index()
    {
        $murids = Murid::with(['kelas', 'kelulusan'])->get();
        return view('dashboard_admin.data_kelulusan', compact('murids'));
    }

    /**
     * Fitur Pencarian AJAX Real-time
     */
    public function search(Request $request)
    {
        $search = $request->get('search');

        $murids = Murid::with(['kelas', 'kelulusan'])
            ->where(function($query) use ($search) {
                $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('nisn', 'LIKE', "%{$search}%")
                      ->orWhere('nis_baru', 'LIKE', "%{$search}%");
            })->get();

        $html = '';
        if ($murids->isEmpty()) {
            $html .= '<tr><td colspan="9" class="text-center text-muted">Data tidak ditemukan</td></tr>';
        } else {
            foreach ($murids as $murid) {
                // Status Badge
                $status = $murid->kelulusan->status ?? '';
                if ($status == 'lulus') {
                    $badge = '<span class="badge bg-success">Lulus</span>';
                } elseif ($status == 'tidak lulus') {
                    $badge = '<span class="badge bg-danger">Tidak Lulus</span>';
                } else {
                    $badge = '<span class="badge bg-secondary">Belum Diatur</span>';
                }

                // Berkas Ijazah & Raport — serve via route privat (bukan asset public)
                $ijazahUrl = (!empty($murid->kelulusan->ijazah) && $kelulusanUuid)
                    ? route('kelulusan.view.ijazah', $kelulusanUuid) : '';
                $raportUrl = (!empty($murid->kelulusan->raport) && $kelulusanUuid)
                    ? route('kelulusan.view.raport', $kelulusanUuid) : '';

                $ijazahBtn = $ijazahUrl
                    ? '<a href="'.$ijazahUrl.'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i></a>'
                    : '-';
                $raportBtn = $raportUrl
                    ? '<a href="'.$raportUrl.'" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-text"></i></a>'
                    : '-';

                // Surat Kelulusan (Menggunakan UUID & Hanya Berupa Icon Saja)
                $kelulusanUuid = $murid->kelulusan->uuid ?? '';
                $suratUrl = (!empty($murid->kelulusan->surat_kelulusan) && $kelulusanUuid) 
                    ? route('kelulusan.view.surat', $kelulusanUuid) 
                    : '';
                
                $suratContent = $suratUrl 
                    ? '<a href="'.$suratUrl.'" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-check-fill"></i></a>' 
                    : '-';

                $kelasName = $murid->kelas->pluck('nama_kelas')->implode(', ') ?: '-';
                $nisBaru   = $murid->nis_baru ?? '-';

                $html .= '<tr>
                    <td>' . e($murid->nisn) . '</td>
                    <td>' . e($nisBaru) . '</td>
                    <td>' . e($murid->nama_lengkap) . '</td>
                    <td>' . e($kelasName) . '</td>
                    <td>' . $badge . '</td>
                    <td class="text-center">' . $ijazahBtn . '</td>
                    <td class="text-center">' . $raportBtn . '</td>
                    <td class="text-center">' . $suratContent . '</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success text-white rounded-pill px-3" 
                            onclick="openEditKelulusan(this, \'' . $kelulusanUuid . '\')"
                            data-ijazah="' . $ijazahUrl . '"
                            data-raport="' . $raportUrl . '"
                            data-surat="' . $suratUrl . '">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    </td>
                </tr>';
            }
        }

        return $html;
    }

    /**
     * Mengambil Detail Data untuk Modal Edit (Menggunakan UUID)
     */
    public function edit($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();
        $murid = Murid::with(['kelas'])->findOrFail($kelulusan->id_murid);
        
        return response()->json([
            'uuid' => $kelulusan->uuid,
            'nama_lengkap' => $murid->nama_lengkap,
            'nisn' => $murid->nisn,
            'nis_baru' => $murid->nis_baru ?? '-',
            'kelas' => $murid->kelas->pluck('nama_kelas')->implode(', ') ?: '-',
            'status' => $kelulusan->status ?? '',
            'tahun_lulus' => $kelulusan->tahun_lulus ?? date('Y'),
        ]);
    }

    /**
     * Memproses Pembaruan Data Kelulusan (Menggunakan UUID)
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:lulus,tidak lulus',
            'tahun_lulus' => 'required|numeric',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'raport' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_kelulusan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();
        $kelulusan->status = $request->status;
        $kelulusan->tahun_lulus = $request->tahun_lulus;

        // FITUR HAPUS IJAZAH
     if ($request->hapus_ijazah == '1' && !$request->hasFile('ijazah')) {
            if ($kelulusan->ijazah) { Storage::delete($kelulusan->ijazah); }
            $kelulusan->ijazah = null;
        }

        // FITUR HAPUS RAPORT
        if ($request->hapus_raport == '1' && !$request->hasFile('raport')) {
            if ($kelulusan->raport) { Storage::delete($kelulusan->raport); }
            $kelulusan->raport = null;
        }

        // FITUR HAPUS SURAT KELULUSAN
        if ($request->hapus_surat_kelulusan == '1' && !$request->hasFile('surat_kelulusan')) {
            if ($kelulusan->surat_kelulusan) { Storage::delete($kelulusan->surat_kelulusan); }
            $kelulusan->surat_kelulusan = null;
        }

        // Pemrosesan unggah berkas baru
        if ($request->hasFile('ijazah')) {
            if ($kelulusan->ijazah) { Storage::delete($kelulusan->ijazah); }
            $kelulusan->ijazah = $request->file('ijazah')->store('kelulusan/ijazah');
        }

        if ($request->hasFile('raport')) {
            if ($kelulusan->raport) { Storage::delete($kelulusan->raport); }
            $kelulusan->raport = $request->file('raport')->store('kelulusan/raport');
        }

        if ($request->hasFile('surat_kelulusan')) {
            if ($kelulusan->surat_kelulusan) {
                Storage::delete($kelulusan->surat_kelulusan);
            }
            $kelulusan->surat_kelulusan = $request->file('surat_kelulusan')->store('kelulusan/surat_kelulusan');
        }

        $kelulusan->save();

        return response()->json(['success' => true, 'message' => 'Data kelulusan & berkas berhasil diperbarui!']);
    }

    /**
     * Serve file ijazah secara aman (privat)
     */
    public function viewIjazah($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();

        if (!$kelulusan->ijazah) {
            abort(404, 'Berkas Ijazah tidak ditemukan.');
        }

        // disk 'local' root = storage/app/private
        $path = Storage::disk('local')->path($kelulusan->ijazah);
        abort_unless(file_exists($path), 404, 'Berkas Ijazah tidak ditemukan.');

        return response()->file($path);
    }

    /**
     * Serve file raport secara aman (privat)
     */
    public function viewRaport($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();

        if (!$kelulusan->raport) {
            abort(404, 'Berkas Raport tidak ditemukan.');
        }

        $path = Storage::disk('local')->path($kelulusan->raport);
        abort_unless(file_exists($path), 404, 'Berkas Raport tidak ditemukan.');

        return response()->file($path);
    }

    /**
     * Mengakses Berkas Privat Surat Kelulusan Secara Aman (Menggunakan UUID)
     */
    public function viewSuratKelulusan($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();

        if (!$kelulusan->surat_kelulusan) {
            abort(404, 'Berkas Surat Kelulusan tidak ditemukan.');
        }

        $path = Storage::disk('local')->path($kelulusan->surat_kelulusan);
        abort_unless(file_exists($path), 404, 'Berkas Surat Kelulusan tidak ditemukan.');

        return response()->file($path);
    }
}