<?php

namespace App\Http\Controllers\Informasi;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;
use App\Models\Informasi\StudiSekolah;
use App\Models\Informasi\StudiKepala;
use App\Models\Informasi\StudiKelas;
use App\Models\Informasi\StudiCatatan;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;
use App\Models\DataMaster\Kelas;
use Illuminate\Http\Request;

class DetailStudiController extends Controller
{
    use RendersUserView;
    // ── Halaman Detail ───────────────────────────────────────────
    public function show($id)
    {
        $studi = StudiSekolah::with(['kepalas', 'kelass.kelas.murid', 'catatans'])
            ->findOrFail($id);

        $guruList  = Guru::where('status', 'aktif')->orderBy('nama_guru')->get(['id', 'nama_guru']);
        $staffList = Staff::where('status', 'aktif')->orderBy('nama_staff')->get(['id', 'nama_staff']);

        // Kelas yang BELUM masuk prodi manapun (atau milik prodi ini sendiri)
        $kelasSudahDipakai = StudiKelas::where('id_studi', '!=', $id)->pluck('id_kelas')->toArray();
        $kelasList = Kelas::whereNotIn('id', $kelasSudahDipakai)->orderBy('nama_kelas')->get();

        // ID kelas yang sudah masuk prodi ini
        $kelasIds = $studi->kelass->pluck('id_kelas')->toArray();

        return $this->renderView('admin.informasi_sekolah.detail_studi', compact(
            'studi', 'guruList', 'staffList', 'kelasList', 'kelasIds'
        ));
    }

    // ── KEPALA PRODI ─────────────────────────────────────────────
    public function storeKepala(Request $request, $id)
    {
        $request->validate([
            'tipe'      => 'required|in:guru,staff',
            'id_sumber' => 'required|integer',
            'jabatan'   => 'nullable|string|max:100',
        ]);

        // Ambil nama cache
        $nama = $this->getNama($request->tipe, $request->id_sumber);
        if (!$nama) return back()->with('error', 'Data tidak ditemukan.');

        // Cegah duplikasi dalam prodi yang sama
        $exists = StudiKepala::where('id_studi', $id)
            ->where('tipe', $request->tipe)
            ->where('id_sumber', $request->id_sumber)
            ->exists();

        if ($exists) return back()->with('error', 'Kepala prodi tersebut sudah terdaftar di program studi ini.');

        StudiKepala::create([
            'id_studi'    => $id,
            'tipe'        => $request->tipe,
            'id_sumber'   => $request->id_sumber,
            'nama_kepala' => $nama,
            'jabatan'     => $request->jabatan ?: 'Kepala Program Studi',
        ]);

        return back()->with('success', 'Kepala program studi berhasil ditambahkan.');
    }

    public function destroyKepala($id, $kepalaId)
    {
        StudiKepala::where('id_studi', $id)->findOrFail($kepalaId)->delete();
        return back()->with('success', 'Kepala program studi berhasil dihapus.');
    }

    // ── KELAS ────────────────────────────────────────────────────
    public function storeKelas(Request $request, $id)
    {
        $request->validate([
            'id_kelases'   => 'required|array|min:1',
            'id_kelases.*' => 'required|integer|exists:kelas,id',
        ]);

        $added = 0;
        $konflik = [];

        foreach ($request->id_kelases as $idKelas) {
            // Cek apakah kelas sudah masuk prodi lain
            $sudahDiProdiLain = StudiKelas::where('id_kelas', $idKelas)
                ->where('id_studi', '!=', $id)
                ->first();

            if ($sudahDiProdiLain) {
                $konflik[] = Kelas::find($idKelas)->nama_kelas ?? "Kelas #$idKelas";
                continue;
            }

            // Cek duplikasi di prodi ini sendiri
            if (!StudiKelas::where('id_studi', $id)->where('id_kelas', $idKelas)->exists()) {
                $kelas = Kelas::findOrFail($idKelas);
                StudiKelas::create([
                    'id_studi'   => $id,
                    'id_kelas'   => $idKelas,
                    'nama_kelas' => $kelas->nama_kelas,
                ]);
                $added++;
            }
        }

        $msg = "{$added} kelas berhasil ditambahkan.";
        if (!empty($konflik)) {
            $msg .= ' Kelas berikut sudah masuk prodi lain dan dilewati: ' . implode(', ', $konflik) . '.';
        }

        return back()->with($added > 0 ? 'success' : 'error', $msg);
    }

    public function destroyKelas($id, $kelasId)
    {
        StudiKelas::where('id_studi', $id)->findOrFail($kelasId)->delete();
        return back()->with('success', 'Kelas berhasil dilepas dari program studi.');
    }

    // ── CATATAN ──────────────────────────────────────────────────
    public function storeCatatan(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        StudiCatatan::create([
            'id_studi' => $id,
            'judul'    => $request->judul,
            'isi'      => $request->isi,
        ]);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function updateCatatan(Request $request, $id, $catatanId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        StudiCatatan::where('id_studi', $id)->findOrFail($catatanId)
            ->update($request->only('judul', 'isi'));

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroyCatatan($id, $catatanId)
    {
        StudiCatatan::where('id_studi', $id)->findOrFail($catatanId)->delete();
        return back()->with('success', 'Catatan berhasil dihapus.');
    }

    // AJAX — data catatan untuk modal edit
    public function getCatatan($id, $catatanId)
    {
        $catatan = StudiCatatan::where('id_studi', $id)->findOrFail($catatanId);
        return response()->json($catatan);
    }

    // AJAX — list nama sesuai tipe untuk dropdown kepala
    public function getSumberByTipe(Request $request)
    {
        $tipe = $request->get('tipe');
        if ($tipe === 'guru') {
            return response()->json(
                Guru::where('status', 'aktif')->orderBy('nama_guru')
                    ->get()->map(fn($g) => ['id' => $g->id, 'nama' => $g->nama_guru])
            );
        }
        return response()->json(
            Staff::where('status', 'aktif')->orderBy('nama_staff')
                ->get()->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_staff])
        );
    }

    // ── Helper ───────────────────────────────────────────────────
    private function getNama(string $tipe, int $id): ?string
    {
        if ($tipe === 'guru') {
            $g = Guru::find($id);
            return $g ? $g->nama_guru : null;
        }
        $s = Staff::find($id);
        return $s ? $s->nama_staff : null;
    }
}
