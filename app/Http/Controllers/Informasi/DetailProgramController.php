<?php

namespace App\Http\Controllers\Informasi;

use App\Http\Controllers\Controller;
use App\Models\Informasi\ProgramSekolah;
use App\Models\Informasi\ProgramPembina;
use App\Models\Informasi\ProgramAnggota;
use App\Models\Informasi\ProgramBagan;
use App\Models\Informasi\ProgramCatatan;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;
use App\Models\DataMaster\Murid;
use Illuminate\Http\Request;

class DetailProgramController extends Controller
{
    /**
     * Halaman detail program sekolah.
     */
    public function show($id)
    {
        $program  = ProgramSekolah::with(['pembinas', 'anggotas.murid.kelas', 'bagans', 'catatans'])->findOrFail($id);

        // Data untuk dropdown pilihan pembina
        $guruList  = Guru::where('status', 'aktif')->orderBy('nama_guru')->get(['id', 'nama_guru']);
        $staffList = Staff::where('status', 'aktif')->orderBy('nama_staff')->get(['id', 'nama_staff']);

        // Data untuk dropdown pilihan murid (konfirmasi = siswa aktif sekolah)
        $muridList = Murid::whereIn('status', ['konfirmasi', 'lulus'])
            ->with('kelas')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nis_baru', 'nisn', 'nama_lengkap']);

        // ID murid yang sudah jadi anggota
        $anggotaIds = $program->anggotas->pluck('id_murid')->toArray();

        return view('admin.informasi_sekolah.detail_program', compact(
            'program', 'guruList', 'staffList', 'muridList', 'anggotaIds'
        ));
    }

    // =========================================================
    // PEMBINA
    // =========================================================

    public function storePembina(Request $request, $id)
    {
        $request->validate([
            'tipe'      => 'required|in:guru,staff',
            'id_sumber' => 'required|integer',
            'peran'     => 'nullable|string|max:100',
        ]);

        // Validasi id_sumber sesuai tipe
        if ($request->tipe === 'guru') {
            Guru::findOrFail($request->id_sumber);
        } else {
            Staff::findOrFail($request->id_sumber);
        }

        // Cegah duplikasi pembina
        $exists = ProgramPembina::where('id_program', $id)
            ->where('tipe', $request->tipe)
            ->where('id_sumber', $request->id_sumber)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Pembina tersebut sudah terdaftar di program ini.');
        }

        ProgramPembina::create([
            'id_program' => $id,
            'tipe'       => $request->tipe,
            'id_sumber'  => $request->id_sumber,
            'peran'      => $request->peran,
        ]);

        return back()->with('success', 'Pembina berhasil ditambahkan.');
    }

    public function destroyPembina($id, $pembinaId)
    {
        ProgramPembina::where('id_program', $id)->findOrFail($pembinaId)->delete();
        return back()->with('success', 'Pembina berhasil dihapus.');
    }

    // =========================================================
    // ANGGOTA
    // =========================================================

    /**
     * Tambah banyak anggota sekaligus (multiple select).
     */
    public function storeAnggota(Request $request, $id)
    {
        $request->validate([
            'id_murids'   => 'required|array|min:1',
            'id_murids.*' => 'required|integer|exists:murid,id',
        ]);

        $added = 0;
        foreach ($request->id_murids as $idMurid) {
            $exists = ProgramAnggota::where('id_program', $id)
                ->where('id_murid', $idMurid)
                ->exists();
            if (!$exists) {
                ProgramAnggota::create(['id_program' => $id, 'id_murid' => $idMurid]);
                $added++;
            }
        }

        return back()->with('success', "{$added} anggota berhasil ditambahkan.");
    }

    public function destroyAnggota($id, $anggotaId)
    {
        ProgramAnggota::where('id_program', $id)->findOrFail($anggotaId)->delete();
        return back()->with('success', 'Anggota berhasil dihapus dari program.');
    }

    // =========================================================
    // BAGAN ORGANISASI
    // =========================================================

    public function storeBagan(Request $request, $id)
    {
        $request->validate([
            'jabatan'       => 'required|string|max:100',
            'tipe_pemegang' => 'required|in:murid,guru,staff',
            'id_pemegang'   => 'required|integer',
            'urutan'        => 'nullable|integer|min:0',
        ]);

        // Ambil nama pemegang secara dinamis
        $nama = $this->getNamaPemegang($request->tipe_pemegang, $request->id_pemegang);

        ProgramBagan::create([
            'id_program'    => $id,
            'jabatan'       => $request->jabatan,
            'tipe_pemegang' => $request->tipe_pemegang,
            'id_pemegang'   => $request->id_pemegang,
            'nama_pemegang' => $nama,
            'urutan'        => $request->urutan ?? 0,
        ]);

        return back()->with('success', 'Entri bagan organisasi berhasil ditambahkan.');
    }

    public function updateBagan(Request $request, $id, $baganId)
    {
        $request->validate([
            'jabatan'       => 'required|string|max:100',
            'tipe_pemegang' => 'required|in:murid,guru,staff',
            'id_pemegang'   => 'required|integer',
            'urutan'        => 'nullable|integer|min:0',
        ]);

        $bagan = ProgramBagan::where('id_program', $id)->findOrFail($baganId);
        $nama  = $this->getNamaPemegang($request->tipe_pemegang, $request->id_pemegang);

        $bagan->update([
            'jabatan'       => $request->jabatan,
            'tipe_pemegang' => $request->tipe_pemegang,
            'id_pemegang'   => $request->id_pemegang,
            'nama_pemegang' => $nama,
            'urutan'        => $request->urutan ?? 0,
        ]);

        return back()->with('success', 'Bagan organisasi berhasil diperbarui.');
    }

    public function destroyBagan($id, $baganId)
    {
        ProgramBagan::where('id_program', $id)->findOrFail($baganId)->delete();
        return back()->with('success', 'Entri bagan organisasi berhasil dihapus.');
    }

    // =========================================================
    // CATATAN
    // =========================================================

    public function storeCatatan(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        ProgramCatatan::create([
            'id_program' => $id,
            'judul'      => $request->judul,
            'isi'        => $request->isi,
        ]);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function updateCatatan(Request $request, $id, $catatanId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        $catatan = ProgramCatatan::where('id_program', $id)->findOrFail($catatanId);
        $catatan->update($request->only('judul', 'isi'));

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroyCatatan($id, $catatanId)
    {
        ProgramCatatan::where('id_program', $id)->findOrFail($catatanId)->delete();
        return back()->with('success', 'Catatan berhasil dihapus.');
    }

    /**
     * AJAX — ambil data catatan untuk modal edit.
     */
    public function getCatatan($id, $catatanId)
    {
        $catatan = ProgramCatatan::where('id_program', $id)->findOrFail($catatanId);
        return response()->json($catatan);
    }

    /**
     * AJAX — ambil data bagan untuk modal edit.
     */
    public function getBagan($id, $baganId)
    {
        $bagan = ProgramBagan::where('id_program', $id)->findOrFail($baganId);
        return response()->json($bagan);
    }

    /**
     * AJAX — ambil daftar nama dari tipe (murid/guru/staff) untuk dropdown dinamis.
     * Jika tipe = murid, hanya tampilkan murid yang sudah jadi anggota program ini.
     * Mendukung parameter: id_program (wajib untuk murid), search (opsional, untuk pencarian), limit (default 10)
     */
    public function getPemegangByTipe(Request $request)
    {
        $tipe      = $request->get('tipe');
        $idProgram = $request->get('id_program');
        $search    = $request->get('search', '');
        $limit     = (int) $request->get('limit', 10);
        $data      = collect();

        if ($tipe === 'murid') {
            // Ambil hanya id_murid yang sudah terdaftar sebagai anggota program ini
            $anggotaIds = $idProgram
                ? ProgramAnggota::where('id_program', $idProgram)->pluck('id_murid')->toArray()
                : [];

            $query = Murid::whereIn('id', $anggotaIds)
                ->whereIn('status', ['konfirmasi', 'lulus'])
                ->with('kelas')
                ->orderBy('nama_lengkap');

            // Filter berdasarkan pencarian
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('nis_baru', 'LIKE', "%{$search}%")
                      ->orWhere('nisn', 'LIKE', "%{$search}%");
                });
            }

            $total  = $query->count();
            $murid  = $query->limit($limit)->get();

            $data = $murid->map(fn($m) => [
                'id'    => $m->id,
                'nama'  => $m->nama_lengkap . ' (' . ($m->kelas->first()->nama_kelas ?? '-') . ')',
                'total' => $total,
            ]);

        } elseif ($tipe === 'guru') {
            $query = Guru::where('status', 'aktif')->orderBy('nama_guru');
            if ($search) {
                $query->where('nama_guru', 'LIKE', "%{$search}%");
            }
            $total = $query->count();
            $data  = $query->limit($limit)->get()->map(fn($g) => [
                'id'    => $g->id,
                'nama'  => $g->nama_guru,
                'total' => $total,
            ]);
        } elseif ($tipe === 'staff') {
            $query = Staff::where('status', 'aktif')->orderBy('nama_staff');
            if ($search) {
                $query->where('nama_staff', 'LIKE', "%{$search}%");
            }
            $total = $query->count();
            $data  = $query->limit($limit)->get()->map(fn($s) => [
                'id'    => $s->id,
                'nama'  => $s->nama_staff,
                'total' => $total,
            ]);
        }

        return response()->json([
            'results'    => $data->values(),
            'pagination' => ['more' => false],
        ]);
    }

    // ── Helper: ambil nama berdasarkan tipe dan id ──────────────
    private function getNamaPemegang(string $tipe, int $id): string
    {
        if ($tipe === 'murid') {
            $m = Murid::with('kelas')->find($id);
            if ($m && in_array($m->status, ['konfirmasi', 'lulus'])) {
                $kelas = $m->kelas->first()->nama_kelas ?? '-';
                return $m->nama_lengkap . ' (' . $kelas . ')';
            }
        } elseif ($tipe === 'guru') {
            $g = Guru::find($id);
            return $g ? $g->nama_guru : '(Guru)';
        } else {
            $s = Staff::find($id);
            return $s ? $s->nama_staff : '(Staff)';
        }
        return '(Tidak ditemukan)';
    }
}
