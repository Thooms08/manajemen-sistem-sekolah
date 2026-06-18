<?php

namespace App\Http\Controllers\Informasi;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\PrestasiDetail;
use App\Models\Informasi\PrestasiMurid;
use App\Models\Informasi\PrestasiCatatan;
use App\Models\DataMaster\Murid;
use Illuminate\Http\Request;

class DetailPrestasiController extends Controller
{
    use RendersUserView;
    public const TINGKAT_LIST = [
        'Sekolah', 'Kecamatan', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional',
    ];

    // ── Halaman Detail ───────────────────────────────────────────
    public function show($id)
    {
        $prestasi = Prestasi::with(['detail', 'murids.murid.kelas', 'catatans'])
            ->findOrFail($id);

        // Murid yang sudah terdaftar di prestasi ini
        $muridIds = $prestasi->murids->pluck('id_murid')->toArray();

        // Load 10 murid pertama untuk tampilan awal (sisanya via AJAX search)
        $muridAwal = Murid::whereIn('status', ['konfirmasi', 'lulus'])
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get(['id', 'nis_baru', 'nisn', 'nama_lengkap']);

        $totalMurid = Murid::whereIn('status', ['konfirmasi', 'lulus'])->count();

        return $this->renderView('admin.informasi_sekolah.detail_prestasi', compact(
            'prestasi', 'muridIds', 'muridAwal', 'totalMurid'
        ));
    }

    // ── UPDATE / UPSERT DETAIL ───────────────────────────────────
    public function updateDetail(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'jenis'               => 'required|in:murid,sekolah',
            'bidang'              => 'nullable|string|max:150',
            'tingkat'             => 'nullable|string|max:100',
            'peringkat'           => 'nullable|string|max:100',
            'penyelenggara'       => 'nullable|string|max:255',
            'tanggal_pelaksanaan' => 'nullable|date',
            'lokasi'              => 'nullable|string|max:255',
            'nama_tim'            => 'nullable|string|max:255',
        ]);

        PrestasiDetail::updateOrCreate(
            ['id_prestasi' => $id],
            $request->only([
                'jenis', 'bidang', 'tingkat', 'peringkat',
                'penyelenggara', 'tanggal_pelaksanaan', 'lokasi', 'nama_tim',
            ])
        );

        return back()->with('success', 'Detail prestasi berhasil disimpan.');
    }

    // ── MURID ────────────────────────────────────────────────────
    public function storeMurid(Request $request, $id)
    {
        $request->validate([
            'id_murids'   => 'required|array|min:1',
            'id_murids.*' => 'required|integer|exists:murid,id',
            'peran'       => 'nullable|string|max:100',
        ]);

        $added   = 0;
        $peran   = $request->peran ?: 'Peserta';

        foreach ($request->id_murids as $idMurid) {
            if (!PrestasiMurid::where('id_prestasi', $id)->where('id_murid', $idMurid)->exists()) {
                PrestasiMurid::create([
                    'id_prestasi' => $id,
                    'id_murid'    => $idMurid,
                    'peran'       => $peran,
                ]);
                $added++;
            }
        }

        return back()->with('success', "{$added} murid berhasil ditambahkan.");
    }

    public function destroyMurid($id, $muridId)
    {
        PrestasiMurid::where('id_prestasi', $id)->findOrFail($muridId)->delete();
        return back()->with('success', 'Murid berhasil dihapus dari prestasi.');
    }

    // ── CATATAN ──────────────────────────────────────────────────
    public function storeCatatan(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        PrestasiCatatan::create([
            'id_prestasi' => $id,
            'judul'       => $request->judul,
            'isi'         => $request->isi,
        ]);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function updateCatatan(Request $request, $id, $catatanId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        PrestasiCatatan::where('id_prestasi', $id)->findOrFail($catatanId)
            ->update($request->only('judul', 'isi'));

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroyCatatan($id, $catatanId)
    {
        PrestasiCatatan::where('id_prestasi', $id)->findOrFail($catatanId)->delete();
        return back()->with('success', 'Catatan berhasil dihapus.');
    }

    // AJAX — data catatan untuk modal edit
    public function getCatatan($id, $catatanId)
    {
        return response()->json(
            PrestasiCatatan::where('id_prestasi', $id)->findOrFail($catatanId)
        );
    }

    // AJAX — cari murid dengan pagination (default 10)
    public function searchMurid(Request $request)
    {
        $search = $request->get('search', '');
        $limit  = (int) $request->get('limit', 10);

        $query = Murid::whereIn('status', ['konfirmasi', 'lulus'])
            ->with('kelas')
            ->orderBy('nama_lengkap');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nis_baru', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $data  = $query->limit($limit)->get()->map(fn($m) => [
            'id'    => $m->id,
            'nama'  => $m->nama_lengkap,
            'nis'   => $m->nis_baru ?? $m->nisn ?? '-',
            'kelas' => $m->kelas->first()->nama_kelas ?? '-',
        ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }
}
