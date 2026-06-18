<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Kelas;
use App\Models\DataMaster\Mapel;
use App\Models\DataMaster\JadwalMengajar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalMengajarController extends Controller
{
    use RendersUserView;
    private const HARI_LIST = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    /**
     * Halaman utama jadwal mengajar.
     * Filter: by guru, by kelas, by hari
     */
    public function index(Request $request)
    {
        $guruList  = Guru::where('status', 'aktif')->orderBy('nama_guru')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();

        // Filter query
        $query = JadwalMengajar::with(['guru', 'mapel', 'kelas'])
            ->when($request->filled('filter_guru'),  fn($q) => $q->where('id_guru', $request->filter_guru))
            ->when($request->filled('filter_kelas'), fn($q) => $q->where('id_kelas', $request->filter_kelas))
            ->when($request->filled('filter_hari'),  fn($q) => $q->where('hari', $request->filter_hari));

        $jadwals = $query->get()->sortBy([
            fn($a, $b) => JadwalMengajar::URUTAN_HARI[$a->hari] <=> JadwalMengajar::URUTAN_HARI[$b->hari],
            fn($a, $b) => strcmp($a->jam_mulai, $b->jam_mulai),
        ])->values();

        // Susun tampilan tabel per hari agar mudah dibaca
        $jadwalPerHari = [];
        foreach (self::HARI_LIST as $hari) {
            $jadwalPerHari[$hari] = $jadwals->where('hari', $hari)->values();
        }

        return $this->renderView('admin.data_master.jadwal_mengajar', compact(
            'guruList', 'kelasList', 'mapelList', 'jadwals', 'jadwalPerHari'
        ));
    }

    /**
     * Simpan jadwal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_guru'    => 'required|exists:guru,id',
            'id_mapel'   => 'required|exists:mapel,id',
            'id_kelas'   => 'required|exists:kelas,id',
            'hari'       => ['required', Rule::in(self::HARI_LIST)],
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'    => 'nullable|string|max:100',
        ], [
            'jam_selesai.after'        => 'Jam selesai harus setelah jam mulai.',
            'jam_mulai.date_format'    => 'Format jam mulai tidak valid (HH:MM).',
            'jam_selesai.date_format'  => 'Format jam selesai tidak valid (HH:MM).',
        ]);

        // Cek bentrok guru
        $bentrokGuru = JadwalMengajar::where('id_guru', $validated['id_guru'])
            ->where('hari', $validated['hari'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('jam_mulai',  [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                         ->where('jam_selesai', '>=', $validated['jam_selesai']);
                  });
            })->exists();

        if ($bentrokGuru) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Guru tersebut sudah memiliki jadwal di hari dan jam yang sama.');
        }

        // Cek bentrok kelas
        $bentrokKelas = JadwalMengajar::where('id_kelas', $validated['id_kelas'])
            ->where('hari', $validated['hari'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('jam_mulai',  [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                         ->where('jam_selesai', '>=', $validated['jam_selesai']);
                  });
            })->exists();

        if ($bentrokKelas) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki pelajaran di hari dan jam yang sama.');
        }

        JadwalMengajar::create($validated);

        return back()->with('success', 'Jadwal mengajar berhasil ditambahkan.');
    }

    /**
     * Ambil data jadwal untuk modal edit (AJAX).
     */
    public function show($id)
    {
        $jadwal = JadwalMengajar::with(['guru', 'mapel', 'kelas'])->findOrFail($id);
        return response()->json($jadwal);
    }

    /**
     * Update jadwal.
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        $validated = $request->validate([
            'id_guru'    => 'required|exists:guru,id',
            'id_mapel'   => 'required|exists:mapel,id',
            'id_kelas'   => 'required|exists:kelas,id',
            'hari'       => ['required', Rule::in(self::HARI_LIST)],
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'    => 'nullable|string|max:100',
        ], [
            'jam_selesai.after'        => 'Jam selesai harus setelah jam mulai.',
            'jam_mulai.date_format'    => 'Format jam mulai tidak valid (HH:MM).',
            'jam_selesai.date_format'  => 'Format jam selesai tidak valid (HH:MM).',
        ]);

        // Cek bentrok guru (kecualikan jadwal ini sendiri)
        $bentrokGuru = JadwalMengajar::where('id_guru', $validated['id_guru'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $id)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('jam_mulai',  [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                         ->where('jam_selesai', '>=', $validated['jam_selesai']);
                  });
            })->exists();

        if ($bentrokGuru) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Guru tersebut sudah memiliki jadwal di hari dan jam yang sama.');
        }

        // Cek bentrok kelas (kecualikan jadwal ini sendiri)
        $bentrokKelas = JadwalMengajar::where('id_kelas', $validated['id_kelas'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $id)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('jam_mulai',  [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                         ->where('jam_selesai', '>=', $validated['jam_selesai']);
                  });
            })->exists();

        if ($bentrokKelas) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki pelajaran di hari dan jam yang sama.');
        }

        $jadwal->update($validated);

        return back()->with('success', 'Jadwal mengajar berhasil diperbarui.');
    }

    /**
     * Hapus jadwal.
     */
    public function destroy($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal mengajar berhasil dihapus.');
    }

    /**
     * Ambil mapel yang diajarkan guru tertentu (AJAX — untuk isi dropdown dinamis di modal).
     */
    public function getMapelByGuru($id_guru)
    {
        $guru = Guru::with('pengajars.mapel')->findOrFail($id_guru);

        $mapels = $guru->pengajars
            ->map(fn($p) => $p->mapel)
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn($m) => ['id' => $m->id, 'nama_mapel' => $m->nama_mapel]);

        return response()->json($mapels);
    }

    /**
     * Ambil kelas yang diajar guru tertentu pada mapel tertentu (AJAX).
     */
    public function getKelasByGuruMapel(Request $request)
    {
        $guru = Guru::with(['pengajars.kelas'])->findOrFail($request->id_guru);

        $kelas = $guru->pengajars
            ->where('id_mapel', $request->id_mapel)
            ->map(fn($p) => $p->kelas)
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn($k) => ['id' => $k->id, 'nama_kelas' => $k->nama_kelas]);

        return response()->json($kelas);
    }
}
