<?php

namespace App\Http\Controllers;

use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Mapel;
use App\Models\DataMaster\Kelas;
use App\Models\DataMaster\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GuruController extends Controller
{
   public function index()
    {
        $mapels = Mapel::all();
        $kelases = Kelas::all(); // Ambil data kelas
        
        // Load relasi pengajars beserta mapel dan kelasnya
        $gurusAktif    = Guru::with(['pengajars.mapel', 'pengajars.kelas'])->where('status', 'aktif')->latest()->get();
        $gurusNonaktif = Guru::with(['pengajars.mapel', 'pengajars.kelas'])->where('status', 'nonaktif')->latest('tanggal_nonaktif')->get();

        return view('admin.data_master.guru', compact('gurusAktif', 'gurusNonaktif', 'mapels', 'kelases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_guru'   => 'required|string|max:255',
            'mapel_ids'   => 'required|array',
            'mapel_ids.*' => 'exists:mapel,id',
            'kelas_ids'   => 'required|array', // Validasi kelas
            'kelas_ids.*' => 'exists:kelas,id',
            'email'       => 'required|email|unique:guru,email',
            'no_whatsapp' => 'required|string|max:15',
            'alamat'      => 'required|string',
        ]);

        $guru = Guru::create(array_merge($request->only(['nama_guru', 'email', 'no_whatsapp', 'alamat']), [
            'status' => 'aktif',
        ]));

        // Looping pasangan mapel dan kelas
        foreach ($request->mapel_ids as $index => $mapel_id) {
            Pengajar::create([
                'id_guru'  => $guru->id,
                'id_mapel' => $mapel_id,
                'id_kelas' => $request->kelas_ids[$index] ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_guru'   => 'required|string|max:255',
            'mapel_ids'   => 'required|array',
            'mapel_ids.*' => 'exists:mapel,id',
            'kelas_ids'   => 'required|array', 
            'kelas_ids.*' => 'exists:kelas,id',
            'email'       => 'required|email|unique:guru,email,' . $id,
            'no_whatsapp' => 'required|string|max:15',
            'alamat'      => 'required|string',
        ]);

        $guru = Guru::findOrFail($id);
        $guru->update($request->only(['nama_guru', 'email', 'no_whatsapp', 'alamat']));

        // Hapus yang lama, simpan kombinasi baru
        Pengajar::where('id_guru', $guru->id)->delete();
        foreach ($request->mapel_ids as $index => $mapel_id) {
            Pengajar::create([
                'id_guru'  => $guru->id,
                'id_mapel' => $mapel_id,
                'id_kelas' => $request->kelas_ids[$index] ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui!');
    }

    /**
     * Nonaktifkan guru (bukan delete permanen) — mirip logika MuridController@destroy.
     * Jika guru adalah wali kelas di satu atau lebih kelas, otomatis dilepas.
     */
    public function destroy($id)
    {
        try {
            $guru   = Guru::findOrFail($id);
            $alasan = request('alasan_nonaktif');
            $suratPath = null;

            if (request()->hasFile('surat_keterangan')) {
                $file     = request()->file('surat_keterangan');
                $fileName = time() . '_surat_guru_' . $guru->id . '.' . $file->getClientOriginalExtension();
                $suratPath = $file->storeAs('nonaktif_guru', $fileName, 'local');
            }

            $guru->update([
                'status'           => 'nonaktif',
                'alasan_nonaktif'  => $alasan,
                'surat_keterangan' => $suratPath,
                'tanggal_nonaktif' => now(),
            ]);

            // Otomatis lepas dari semua kelas yang dia pegang sebagai wali kelas
            \App\Models\Kelas::where('id_wali_kelas', $guru->id)
                ->update(['id_wali_kelas' => null]);

            return redirect()->back()->with('success', 'Guru berhasil dipindahkan ke data nonaktif dan dilepas dari semua wali kelas.');
        } catch (\Exception $e) {
            Log::error('Guru Nonaktif Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Pulihkan guru nonaktif kembali ke aktif.
     */
    public function restore($id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->surat_keterangan && Storage::disk('local')->exists($guru->surat_keterangan)) {
            Storage::disk('local')->delete($guru->surat_keterangan);
        }

        $guru->update([
            'status'           => 'aktif',
            'alasan_nonaktif'  => null,
            'surat_keterangan' => null,
            'tanggal_nonaktif' => null,
        ]);

        return redirect()->back()->with('success', 'Guru berhasil dipulihkan ke data aktif.');
    }

    /**
     * Download surat keterangan nonaktif guru.
     */
    public function downloadSurat($id)
    {
        $guru = Guru::findOrFail($id);

        if (!$guru->surat_keterangan || !Storage::disk('local')->exists($guru->surat_keterangan)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $guru->surat_keterangan,
            'Surat_Keterangan_' . str_replace(' ', '_', $guru->nama_guru) . '.' . pathinfo($guru->surat_keterangan, PATHINFO_EXTENSION)
        );
    }

    /**
     * AJAX Search — sadar tab (aktif / nonaktif).
     */
   public function search(Request $request)
    {
        $query  = $request->get('search');
        $tab    = $request->get('tab', 'aktif');
        $output = '';

        $guruQuery = Guru::with(['pengajars.mapel', 'pengajars.kelas'])->where('status', $tab === 'nonaktif' ? 'nonaktif' : 'aktif')
            ->where(function ($q) use ($query) {
                $q->where('nama_guru', 'LIKE', '%' . $query . '%')
                  ->orWhere('email', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('pengajars.mapel', function($subQ) use ($query) {
                      $subQ->where('nama_mapel', 'LIKE', '%' . $query . '%');
                  })
                  ->orWhereHas('pengajars.kelas', function($subQ) use ($query) {
                      $subQ->where('nama_kelas', 'LIKE', '%' . $query . '%');
                  });
            });

        $gurus = $guruQuery->get();

        if ($gurus->count() > 0) {
            foreach ($gurus as $index => $g) {
                // Render badge unik HTML untuk Mapel dan Kelas
                $mapelBadges = $g->pengajars->pluck('mapel.nama_mapel')->filter()->unique()
                    ->map(fn($m) => '<span class="badge bg-secondary mb-1">'.e($m).'</span>')->implode(' ');
                $kelasBadges = $g->pengajars->pluck('kelas.nama_kelas')->filter()->unique()
                    ->map(fn($k) => '<span class="badge bg-info text-dark mb-1">'.e($k).'</span>')->implode(' ');

                // Data Array Object [{id_mapel: 1, id_kelas: 2}] untuk Modal Edit
                $pengajarData = $g->pengajars->map(function($p) {
                    return ['id_mapel' => $p->id_mapel, 'id_kelas' => $p->id_kelas];
                })->toJson();

                if ($tab === 'nonaktif') {
                    $output .= '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td class="fw-bold">' . e($g->nama_guru) . '</td>
                        <td><div class="d-flex flex-wrap gap-1">' . $mapelBadges . '</div></td>
                        <td><div class="d-flex flex-wrap gap-1">' . $kelasBadges . '</div></td>
                        <td>' . e($g->email) . '</td>
                        <td>' . e($g->no_whatsapp) . '</td>
                        <td><span class="badge bg-danger bg-opacity-10 text-danger px-2">' . e($g->alasan_nonaktif ?? '-') . '</span></td>
                        <td class="text-muted small">' . ($g->tanggal_nonaktif ? \Carbon\Carbon::parse($g->tanggal_nonaktif)->format('d M Y') : '-') . '</td>
                        <td class="text-center">
                            ' . ($g->surat_keterangan ? '<a href="' . route('guru.download-surat', $g->id) . '" class="btn btn-sm btn-outline-secondary border-0"><i class="bi bi-file-earmark-arrow-down"></i></a>' : '') . '
                            <form action="' . route('guru.restore', $g->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                <button class="btn btn-sm btn-outline-success border-0" onclick="return confirm(\'Pulihkan guru ini ke data aktif?\')"><i class="bi bi-arrow-counterclockwise"></i></button>
                            </form>
                        </td>
                    </tr>';
                } else {
                    $output .= '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td class="fw-bold">' . e($g->nama_guru) . '</td>
                        <td><div class="d-flex flex-wrap gap-1">' . $mapelBadges . '</div></td>
                        <td><div class="d-flex flex-wrap gap-1">' . $kelasBadges . '</div></td>
                        <td>' . e($g->email) . '</td>
                        <td>' . e($g->no_whatsapp) . '</td>
                        <td>' . \Illuminate\Support\Str::limit($g->alamat, 30) . '</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success border-0" onclick=\'openEditModal("' . $g->id . '", "' . addslashes($g->nama_guru) . '", ' . $pengajarData . ', "' . addslashes($g->email) . '", "' . addslashes($g->no_whatsapp) . '", "' . addslashes($g->alamat) . '")\'><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-outline-danger border-0" onclick="bukaModalNonaktif(' . $g->id . ', \'' . addslashes($g->nama_guru) . '\')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>';
                }
            }
        } else {
            $cols = $tab === 'nonaktif' ? 9 : 8; // Ditambah 1 karena ada kolom Kelas
            $output = '<tr><td colspan="' . $cols . '" class="text-center py-4 text-muted">Data guru tidak ditemukan</td></tr>';
        }

        return response($output);
    }
    // ── Route untuk dashboard Guru (pelanggaran) ──────────────────────────

    public function pelanggaran()
    {
        $kelas   = DB::table('kelas')->get();
        $aturans = DB::table('aturan_pelanggaran')->get();

        $riwayatPelanggaran = DB::table('pelanggaran_murid')
            ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
            ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
            ->leftJoin('murid_kelas', 'murid.id', '=', 'murid_kelas.id_murid')
            ->leftJoin('kelas', 'murid_kelas.id_kelas', '=', 'kelas.id')
            ->select(
                'pelanggaran_murid.*',
                'murid.nama_lengkap',
                'murid.nisn',
                'aturan_pelanggaran.nama_pelanggaran',
                'aturan_pelanggaran.skor',
                'kelas.nama_kelas'
            )
            ->orderBy('pelanggaran_murid.created_at', 'desc')
            ->get();

        return view('dashboard_guru.pelanggaran', compact('kelas', 'aturans', 'riwayatPelanggaran'));
    }

    public function searchPelanggaran(Request $request)
    {
        $query = $request->get('search');

        $riwayatPelanggaran = DB::table('pelanggaran_murid')
            ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
            ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
            ->leftJoin('murid_kelas', 'murid.id', '=', 'murid_kelas.id_murid')
            ->leftJoin('kelas', 'murid_kelas.id_kelas', '=', 'kelas.id')
            ->select('pelanggaran_murid.*', 'murid.nama_lengkap', 'murid.nisn', 'aturan_pelanggaran.nama_pelanggaran', 'aturan_pelanggaran.skor', 'kelas.nama_kelas')
            ->where(function ($q) use ($query) {
                $q->where('murid.nama_lengkap', 'LIKE', '%' . $query . '%')
                  ->orWhere('murid.nisn', 'LIKE', '%' . $query . '%')
                  ->orWhere('aturan_pelanggaran.nama_pelanggaran', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('pelanggaran_murid.created_at', 'desc')
            ->get();

        $output = '';
        if ($riwayatPelanggaran->count() > 0) {
            foreach ($riwayatPelanggaran as $rp) {
                if ($rp->status == 'pending') {
                    $statusBadge = '<span class="badge bg-warning text-dark px-3 py-2">Pending</span>';
                } elseif ($rp->status == 'konfirmasi') {
                    $statusBadge = '<span class="badge bg-success px-3 py-2">Dikonfirmasi</span>';
                } else {
                    $statusBadge = '<span class="badge bg-danger px-3 py-2">Ditolak</span>';
                }

                $output .= '
                <tr>
                    <td>
                        <div class="fw-bold text-dark">' . $rp->nama_lengkap . '</div>
                        <small class="text-muted">NISN: ' . $rp->nisn . '</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary mb-1">' . ($rp->nama_kelas ?? 'Tanpa Kelas') . '</span>
                        <div>' . $rp->nama_pelanggaran . '</div>
                    </td>
                    <td class="text-center"><span class="text-danger fw-bold">+' . $rp->skor . '</span></td>
                    <td class="text-center">' . $statusBadge . '</td>
                    <td><div class="small">' . date('d M Y', strtotime($rp->created_at)) . '</div></td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }
}
