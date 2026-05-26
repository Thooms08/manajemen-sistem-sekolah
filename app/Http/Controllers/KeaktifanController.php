<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Murid;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KeaktifanController extends Controller
{
    public function index(Request $request)
    {
        $kelas = DB::table('kelas')->get();
        $filterTanggal = $request->get('tanggal', date('Y-m-d'));

        // Query untuk mengambil data riwayat keaktifan
        $riwayat = DB::table('keaktifan_murid')
            ->join('keaktifans', 'keaktifan_murid.keaktifan_id', '=', 'keaktifans.id')
            ->join('murid', 'keaktifan_murid.murid_id', '=', 'murid.id')
            ->join('kelas', 'keaktifans.id_kelas', '=', 'kelas.id')
            ->select('murid.nama_lengkap', 'kelas.nama_kelas', 'keaktifans.nama_keaktifan', 'keaktifans.tanggal', 'keaktifan_murid.is_active','keaktifan_murid.keaktifan_id')
            ->whereDate('keaktifans.tanggal', $filterTanggal)
            ->orderBy('keaktifans.created_at', 'desc')
            ->get();

        return view('dashboard_guru.keaktifan', compact('kelas', 'riwayat', 'filterTanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'nama_keaktifan' => 'required',
            'foto' => 'nullable|image|max:2048'
        ]);

        // --- PERBAIKAN: Cari ID dari tabel guru berdasarkan Auth User ID ---
        $guru = DB::table('guru')->where('id_user', Auth::id())->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru Anda tidak ditemukan di sistem.');
        }

        DB::transaction(function () use ($request, $guru) {
            $path = null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('keaktifan'), $filename);
                $path = 'keaktifan/' . $filename;
            }

            // Gunakan $guru->id dari tabel guru
            $keaktifanId = DB::table('keaktifans')->insertGetId([
                'id_guru' => $guru->id, 
                'nama_keaktifan' => $request->nama_keaktifan,
                'id_kelas' => $request->id_kelas,
                'tanggal' => now()->format('Y-m-d'),
                'foto' => $path,
                'keterangan' => $request->keterangan,
                'created_at' => now(),
            ]);

            $semuaMurid = DB::table('murid_kelas')
                ->where('id_kelas', $request->id_kelas)
                ->pluck('id_murid');

            $tidakAktifIds = $request->murid_ids ?? [];

            foreach ($semuaMurid as $muridId) {
                DB::table('keaktifan_murid')->insert([
                    'keaktifan_id' => $keaktifanId,
                    'murid_id' => $muridId,
                    'is_active' => in_array($muridId, $tidakAktifIds) ? 0 : 1,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Data keaktifan berhasil diproses.');
    }

    public function edit($id)
    {
        $keaktifan = DB::table('keaktifans')->where('id', $id)->first();
        
        $murids = DB::table('murid_kelas')
            ->join('murid', 'murid_kelas.id_murid', '=', 'murid.id')
            ->where('murid_kelas.id_kelas', $keaktifan->id_kelas)
            ->select('murid.id', 'murid.nama_lengkap')
            ->get();

        $tidakAktifIds = DB::table('keaktifan_murid')
            ->where('keaktifan_id', $id)
            ->where('is_active', 0)
            ->pluck('murid_id')
            ->toArray();

        return response()->json([
            'keaktifan' => $keaktifan,
            'murids' => $murids,
            'tidakAktifIds' => $tidakAktifIds
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            DB::table('keaktifans')->where('id', $id)->update([
                'nama_keaktifan' => $request->nama_keaktifan,
                'keterangan' => $request->keterangan,
                'updated_at' => now(),
            ]);

            $keaktifan = DB::table('keaktifans')->where('id', $id)->first();
            $semuaMurid = DB::table('murid_kelas')
                ->where('id_kelas', $keaktifan->id_kelas)
                ->pluck('id_murid');

            $tidakAktifIds = $request->murid_ids ?? [];

            DB::table('keaktifan_murid')->where('keaktifan_id', $id)->delete();

            foreach ($semuaMurid as $muridId) {
                DB::table('keaktifan_murid')->insert([
                    'keaktifan_id' => $id,
                    'murid_id' => $muridId,
                    'is_active' => in_array($muridId, $tidakAktifIds) ? 0 : 1,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Status keaktifan berhasil diperbarui.');
    }
}