<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Traits\RendersUserView;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Kelas;
use App\Models\DataMaster\Mapel;
use App\Models\DataMaster\Pengajar;
use App\Models\DataMaster\JadwalMengajar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalMengajarController extends Controller
{
    use RendersUserView;

    private const HARI_LIST = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    /**
     * Halaman utama jadwal mengajar.
     */
    public function index(Request $request)
    {
        $guruList  = Guru::where('status', 'aktif')->orderBy('nama_guru')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();

        $query = JadwalMengajar::with(['guru', 'mapel', 'kelas'])
            ->when($request->filled('filter_guru'),  fn($q) => $q->where('id_guru',  $request->filter_guru))
            ->when($request->filled('filter_kelas'), fn($q) => $q->where('id_kelas', $request->filter_kelas))
            ->when($request->filled('filter_mapel'), fn($q) => $q->where('id_mapel', $request->filter_mapel))
            ->when($request->filled('filter_hari'),  fn($q) => $q->where('hari',     $request->filter_hari));

        $jadwals = $query->get()->sortBy([
            fn($a, $b) => JadwalMengajar::URUTAN_HARI[$a->hari] <=> JadwalMengajar::URUTAN_HARI[$b->hari],
            fn($a, $b) => strcmp($a->jam_mulai, $b->jam_mulai),
        ])->values();

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
            'id_guru'     => 'required|exists:guru,id',
            'id_mapel'    => 'required|exists:mapel,id',
            'id_kelas'    => 'required|exists:kelas,id',
            'hari'        => ['required', Rule::in(self::HARI_LIST)],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'     => 'nullable|string|max:100',
        ], [
            'id_mapel.required'            => 'Mata pelajaran wajib dipilih.',
            'id_kelas.required'            => 'Kelas wajib dipilih.',
            'jam_selesai.after'            => 'Jam selesai harus setelah jam mulai.',
            'jam_mulai.date_format'        => 'Format jam mulai tidak valid (HH:MM).',
            'jam_selesai.date_format'      => 'Format jam selesai tidak valid (HH:MM).',
        ]);

        // Pastikan kombinasi guru-mapel-kelas terdaftar di tabel pengajar
        $pengajarValid = Pengajar::where('id_guru',  $validated['id_guru'])
                                 ->where('id_mapel', $validated['id_mapel'])
                                 ->where('id_kelas', $validated['id_kelas'])
                                 ->exists();

        if (! $pengajarValid) {
            return back()->withInput()
                ->with('error', 'Kombinasi guru, mata pelajaran, dan kelas tidak valid. Pastikan sudah terdaftar di data guru.');
        }

        // Cek bentrok: guru yang sama, hari & jam tumpang tindih
        if ($this->adaBentrokGuru($validated['id_guru'], $validated['hari'], $validated['jam_mulai'], $validated['jam_selesai'])) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Guru tersebut sudah memiliki jadwal di hari dan jam yang sama.');
        }

        // Cek bentrok: kelas yang sama, hari & jam tumpang tindih
        if ($this->adaBentrokKelas($validated['id_kelas'], $validated['hari'], $validated['jam_mulai'], $validated['jam_selesai'])) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki pelajaran lain di hari dan jam yang sama.');
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
            'id_guru'     => 'required|exists:guru,id',
            'id_mapel'    => 'required|exists:mapel,id',
            'id_kelas'    => 'required|exists:kelas,id',
            'hari'        => ['required', Rule::in(self::HARI_LIST)],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'     => 'nullable|string|max:100',
        ], [
            'id_mapel.required'            => 'Mata pelajaran wajib dipilih.',
            'id_kelas.required'            => 'Kelas wajib dipilih.',
            'jam_selesai.after'            => 'Jam selesai harus setelah jam mulai.',
            'jam_mulai.date_format'        => 'Format jam mulai tidak valid (HH:MM).',
            'jam_selesai.date_format'      => 'Format jam selesai tidak valid (HH:MM).',
        ]);

        // Pastikan kombinasi guru-mapel-kelas terdaftar di tabel pengajar
        $pengajarValid = Pengajar::where('id_guru',  $validated['id_guru'])
                                 ->where('id_mapel', $validated['id_mapel'])
                                 ->where('id_kelas', $validated['id_kelas'])
                                 ->exists();

        if (! $pengajarValid) {
            return back()->withInput()
                ->with('error', 'Kombinasi guru, mata pelajaran, dan kelas tidak valid. Pastikan sudah terdaftar di data guru.');
        }

        // Cek bentrok guru (kecualikan jadwal ini sendiri)
        if ($this->adaBentrokGuru($validated['id_guru'], $validated['hari'], $validated['jam_mulai'], $validated['jam_selesai'], $id)) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Guru tersebut sudah memiliki jadwal di hari dan jam yang sama.');
        }

        // Cek bentrok kelas (kecualikan jadwal ini sendiri)
        if ($this->adaBentrokKelas($validated['id_kelas'], $validated['hari'], $validated['jam_mulai'], $validated['jam_selesai'], $id)) {
            return back()->withInput()
                ->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki pelajaran lain di hari dan jam yang sama.');
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
     * Halaman poster jadwal mengajar.
     */
    public function showPoster(Request $request)
    {
        $guruList  = Guru::where('status', 'aktif')->orderBy('nama_guru')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $mapHariEn   = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariHariIni = $mapHariEn[now()->englishDayOfWeek] ?? 'Senin';
        $validHari   = self::HARI_LIST;
        $hariDipilih = $request->filled('hari') && in_array($request->hari, $validHari)
            ? $request->hari
            : (in_array($hariHariIni, $validHari) ? $hariHariIni : 'Senin');

        $jadwalHariIni = JadwalMengajar::with(['guru', 'mapel', 'kelas'])
            ->where('hari', $hariDipilih)
            ->when($request->filled('filter_guru'),  fn($q) => $q->where('id_guru',  $request->filter_guru))
            ->when($request->filled('filter_kelas'), fn($q) => $q->where('id_kelas', $request->filter_kelas))
            ->orderBy('jam_mulai')
            ->get();

        return $this->renderView('admin.data_master.show_jadwal_mengajar', compact(
            'guruList', 'kelasList',
            'jadwalHariIni', 'hariDipilih', 'hariHariIni', 'validHari'
        ));
    }

    /**
     * AJAX — ambil daftar mapel yang diajarkan guru tertentu.
     * Mengembalikan array [{id, nama_mapel}] agar bisa dipakai dropdown.
     */
    public function getMapelByGuru($id_guru)
    {
        $mapels = Pengajar::where('id_guru', $id_guru)
            ->with('mapel')
            ->get()
            ->pluck('mapel')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn($m) => ['id' => $m->id, 'nama_mapel' => $m->nama_mapel]);

        return response()->json($mapels);
    }

    /**
     * AJAX — ambil daftar kelas yang diajarkan guru tertentu UNTUK mapel tertentu.
     * Mengembalikan array [{id, nama_kelas}].
     */
    public function getKelasByGuruMapel(Request $request)
    {
        $kelas = Pengajar::where('id_guru',  $request->id_guru)
            ->where('id_mapel', $request->id_mapel)
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn($k) => ['id' => $k->id, 'nama_kelas' => $k->nama_kelas]);

        return response()->json($kelas);
    }

    // ────────────────────────────────────────────────────────────────
    // Private helpers
    // ────────────────────────────────────────────────────────────────

    /**
     * Cek apakah guru punya jadwal yang tumpang tindih di hari & jam tertentu.
     */
    private function adaBentrokGuru(string $idGuru, string $hari, string $jamMulai, string $jamSelesai, ?int $kecualiId = null): bool
    {
        return JadwalMengajar::where('id_guru', $idGuru)
            ->where('hari', $hari)
            ->when($kecualiId, fn($q) => $q->where('id', '!=', $kecualiId))
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where(fn($q2) => $q2->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $jamMulai));
            })
            ->exists();
    }

    /**
     * Cek apakah kelas sudah ada jadwal lain yang tumpang tindih.
     */
    private function adaBentrokKelas(string $idKelas, string $hari, string $jamMulai, string $jamSelesai, ?int $kecualiId = null): bool
    {
        return JadwalMengajar::where('id_kelas', $idKelas)
            ->where('hari', $hari)
            ->when($kecualiId, fn($q) => $q->where('id', '!=', $kecualiId))
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where(fn($q2) => $q2->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $jamMulai));
            })
            ->exists();
    }
}
