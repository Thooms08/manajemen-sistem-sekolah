<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Traits\RendersUserView;
use App\Models\DataMaster\Murid;
use App\Models\Dokumen\Kelulusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class KelulusanController extends Controller
{
    use RendersUserView;
    /**
     * Tampilan Utama Halaman Kelulusan.
     * Hanya load data minimal — detail berkas di-lazy load saat buka modal edit.
     */
    public function index()
    {
        // Ambil murid aktif (konfirmasi) dan yang sudah lulus — eager load kelulusan
        $murids = Murid::whereIn('status', ['konfirmasi', 'lulus'])
            ->with(['kelas:id,nama_kelas', 'kelulusan:id,uuid,id_murid,status,tahun_lulus,ijazah,raport,surat_kelulusan'])
            ->orderBy('nama_lengkap')
            ->get();

        // Pastikan semua murid sudah punya record kelulusan (auto-create jika belum ada)
        $this->ensureKelulusanRecords($murids);

        // Reload dengan data kelulusan yang sudah pasti ada
        $murids->load('kelulusan:id,uuid,id_murid,status,tahun_lulus,ijazah,raport,surat_kelulusan');

        return $this->renderView('admin.data_master.data_kelulusan', compact('murids'));
    }

    /**
     * Pastikan setiap murid sudah punya 1 record di tabel kelulusan.
     * Insert bulk untuk yang belum ada — jauh lebih cepat dari looping satu-satu.
     */
    private function ensureKelulusanRecords($murids): void
    {
        // Kumpulkan id_murid yang belum punya record kelulusan
        $missingIds = $murids
            ->filter(fn($m) => is_null($m->kelulusan))
            ->pluck('id')
            ->toArray();

        if (empty($missingIds)) return;

        // Bulk insert via raw DB — jauh lebih cepat dari looping Kelulusan::create()
        $now  = now();
        $rows = array_map(fn($id) => [
            'uuid'       => (string) Str::uuid(),
            'id_murid'   => $id,
            'status'     => null,
            'tahun_lulus'=> null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $missingIds);

        // Insert dengan chunk 100 agar tidak overflow kalau murid banyak
        foreach (array_chunk($rows, 100) as $chunk) {
            \Illuminate\Support\Facades\DB::connection('dokumen_db')
                ->table('kelulusan')
                ->insertOrIgnore($chunk);
        }
    }

    /**
     * Fitur Pencarian AJAX Real-time
     */
    public function search(Request $request)
    {
        $search = $request->get('search');

        $murids = Murid::whereIn('status', ['konfirmasi', 'lulus'])
            ->with(['kelas:id,nama_kelas', 'kelulusan:id,uuid,id_murid,status,tahun_lulus,ijazah,raport,surat_kelulusan'])
            ->where(function ($query) use ($search) {
                $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('nisn', 'LIKE', "%{$search}%")
                      ->orWhere('nis_baru', 'LIKE', "%{$search}%");
            })
            ->orderBy('nama_lengkap')
            ->get();

        $html = '';
        if ($murids->isEmpty()) {
            $html = '<tr><td colspan="9" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>';
            return $html;
        }

        foreach ($murids as $murid) {
            // UUID — deklarasi dulu sebelum dipakai
            $kelulusanUuid = $murid->kelulusan->uuid ?? '';

            // Status Badge
            $status = $murid->kelulusan->status ?? '';
            if ($status === 'lulus') {
                $badge = '<span class="badge bg-success">Lulus</span>';
            } elseif ($status === 'tidak lulus') {
                $badge = '<span class="badge bg-danger">Tidak Lulus</span>';
            } else {
                $badge = '<span class="badge bg-secondary">Belum Diatur</span>';
            }

            // URL berkas — gunakan UUID yang sudah dideklarasi
            $ijazahUrl = (!empty($murid->kelulusan->ijazah) && $kelulusanUuid)
                ? route('kelulusan.view.ijazah', $kelulusanUuid) : '';
            $raportUrl = (!empty($murid->kelulusan->raport) && $kelulusanUuid)
                ? route('kelulusan.view.raport', $kelulusanUuid) : '';
            $suratUrl  = (!empty($murid->kelulusan->surat_kelulusan) && $kelulusanUuid)
                ? route('kelulusan.view.surat', $kelulusanUuid) : '';

            $ijazahBtn = $ijazahUrl
                ? '<a href="'.$ijazahUrl.'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i></a>'
                : '-';
            $raportBtn = $raportUrl
                ? '<a href="'.$raportUrl.'" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-text"></i></a>'
                : '-';
            $suratBtn  = $suratUrl
                ? '<a href="'.$suratUrl.'" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-check-fill"></i></a>'
                : '-';

            $kelasName = $murid->kelas->pluck('nama_kelas')->implode(', ') ?: '-';
            $nisBaru   = e($murid->nis_baru ?? '-');

            $html .= '<tr>
                <td>' . e($murid->nisn) . '</td>
                <td>' . $nisBaru . '</td>
                <td>' . e($murid->nama_lengkap) . '</td>
                <td>' . e($kelasName) . '</td>
                <td>' . $badge . '</td>
                <td class="text-center">' . $ijazahBtn . '</td>
                <td class="text-center">' . $raportBtn . '</td>
                <td class="text-center">' . $suratBtn . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3"
                        onclick="openEditKelulusan(this, \'' . $kelulusanUuid . '\')"
                        data-ijazah="' . $ijazahUrl . '"
                        data-raport="' . $raportUrl . '"
                        data-surat="' . $suratUrl . '">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </td>
            </tr>';
        }

        return $html;
    }

    /**
     * Mengambil Detail Data untuk Modal Edit (via UUID)
     */
    public function edit($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)
            ->select('id', 'uuid', 'id_murid', 'status', 'tahun_lulus')
            ->firstOrFail();

        $murid = Murid::with(['kelas:id,nama_kelas'])
            ->select('id', 'nama_lengkap', 'nisn', 'nis_baru')
            ->findOrFail($kelulusan->id_murid);

        return response()->json([
            'uuid'         => $kelulusan->uuid,
            'nama_lengkap' => $murid->nama_lengkap,
            'nisn'         => $murid->nisn,
            'nis_baru'     => $murid->nis_baru ?? '-',
            'kelas'        => $murid->kelas->pluck('nama_kelas')->implode(', ') ?: '-',
            'status'       => $kelulusan->status ?? '',
            'tahun_lulus'  => $kelulusan->tahun_lulus ?? date('Y'),
        ]);
    }

    /**
     * Memproses Pembaruan Data Kelulusan (via UUID)
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'status'          => 'required|in:lulus,tidak lulus',
            'tahun_lulus'     => 'required|numeric|min:2000|max:2100',
            'ijazah'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'raport'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_kelulusan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $kelulusan = Kelulusan::where('uuid', $uuid)->firstOrFail();
        $kelulusan->status      = $request->status;
        $kelulusan->tahun_lulus = $request->tahun_lulus;

        // ── Hapus berkas lama jika diminta ──────────────────────────────────
        if ($request->hapus_ijazah == '1' && !$request->hasFile('ijazah')) {
            if ($kelulusan->ijazah) Storage::disk('local')->delete($kelulusan->ijazah);
            $kelulusan->ijazah = null;
        }
        if ($request->hapus_raport == '1' && !$request->hasFile('raport')) {
            if ($kelulusan->raport) Storage::disk('local')->delete($kelulusan->raport);
            $kelulusan->raport = null;
        }
        if ($request->hapus_surat_kelulusan == '1' && !$request->hasFile('surat_kelulusan')) {
            if ($kelulusan->surat_kelulusan) Storage::disk('local')->delete($kelulusan->surat_kelulusan);
            $kelulusan->surat_kelulusan = null;
        }

        // ── Upload berkas baru ───────────────────────────────────────────────
        if ($request->hasFile('ijazah')) {
            if ($kelulusan->ijazah) Storage::disk('local')->delete($kelulusan->ijazah);
            $kelulusan->ijazah = $request->file('ijazah')
                ->store('kelulusan/ijazah', 'local');
        }
        if ($request->hasFile('raport')) {
            if ($kelulusan->raport) Storage::disk('local')->delete($kelulusan->raport);
            $kelulusan->raport = $request->file('raport')
                ->store('kelulusan/raport', 'local');
        }
        if ($request->hasFile('surat_kelulusan')) {
            if ($kelulusan->surat_kelulusan) Storage::disk('local')->delete($kelulusan->surat_kelulusan);
            $kelulusan->surat_kelulusan = $request->file('surat_kelulusan')
                ->store('kelulusan/surat_kelulusan', 'local');
        }

        $kelulusan->save();

        // ── Sinkronkan kolom status di tabel murid (data mutlak) ────────────
        // Jika status kelulusan = 'lulus' → update status murid menjadi 'lulus'
        // Jika status kelulusan = 'tidak lulus' → kembalikan status murid ke 'konfirmasi'
        $muridStatus = ($kelulusan->status === 'lulus') ? 'lulus' : 'konfirmasi';
        Murid::where('id', $kelulusan->id_murid)
            ->update(['status' => $muridStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Data kelulusan & berkas berhasil diperbarui!',
        ]);
    }

    /**
     * Serve file ijazah secara aman (privat)
     */
    public function viewIjazah($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)
            ->select('ijazah')
            ->firstOrFail();

        abort_if(empty($kelulusan->ijazah), 404, 'Berkas Ijazah tidak ditemukan.');

        $path = Storage::disk('local')->path($kelulusan->ijazah);
        abort_unless(file_exists($path), 404, 'Berkas Ijazah tidak ditemukan.');

        return response()->file($path);
    }

    /**
     * Serve file raport secara aman (privat)
     */
    public function viewRaport($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)
            ->select('raport')
            ->firstOrFail();

        abort_if(empty($kelulusan->raport), 404, 'Berkas Raport tidak ditemukan.');

        $path = Storage::disk('local')->path($kelulusan->raport);
        abort_unless(file_exists($path), 404, 'Berkas Raport tidak ditemukan.');

        return response()->file($path);
    }

    /**
     * Serve file surat kelulusan secara aman (privat)
     */
    public function viewSuratKelulusan($uuid)
    {
        $kelulusan = Kelulusan::where('uuid', $uuid)
            ->select('surat_kelulusan')
            ->firstOrFail();

        abort_if(empty($kelulusan->surat_kelulusan), 404, 'Berkas Surat Kelulusan tidak ditemukan.');

        $path = Storage::disk('local')->path($kelulusan->surat_kelulusan);
        abort_unless(file_exists($path), 404, 'Berkas Surat Kelulusan tidak ditemukan.');

        return response()->file($path);
    }
}
