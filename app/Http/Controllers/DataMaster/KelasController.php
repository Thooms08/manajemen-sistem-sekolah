<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Traits\RendersUserView;


use Illuminate\Http\Request;
use App\Models\DataMaster\Kelas;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\Guru;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KelasController extends Controller
{
    use RendersUserView;
    public function index()
    {
        // Load relasi murid (count) dan waliKelas agar bisa dicek kondisi di view
        $kelas = Kelas::withCount('murid')->with('waliKelas')->get();

        // Mengambil murid yang BELUM memiliki kelas (agar tidak error di blade)
        $muridTersedia = \App\Models\DataMaster\Murid::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('murid_kelas')
                  ->whereRaw('murid_kelas.id_murid = murid.id');
        })->get();

        return $this->renderView('admin.data_master.kelas', compact('kelas', 'muridTersedia'));
    }

    // HALAMAN BARU: Menampilkan detail kelas dan daftar murid di dalamnya
    public function show($id)
    {
        $kelas = Kelas::with([
            'murid',
            'waliKelas.pengajars.mapel', // wali kelas beserta mapel yang dia ajarkan
            'pengajars.guru',            // semua pengajar di kelas ini
            'pengajars.mapel',           // beserta mapel masing-masing
        ])->where('uuid', $id)->firstOrFail();

        // Ambil murid yang belum punya kelas untuk pilihan "Tambah Murid"
        $muridTersedia = Murid::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('murid_kelas')
                  ->whereRaw('murid_kelas.id_murid = murid.id');
        })->get();

        // Ambil hanya guru aktif untuk modal "Wali Kelas"
        $semuaGuru = Guru::where('status', 'aktif')->orderBy('nama_guru')->get();

        return $this->renderView('admin.data_master.detail_kelas', compact('kelas', 'muridTersedia', 'semuaGuru'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kelas' => 'required|string|max:255']);
        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'id_murid' => 'required|array',
            'id_murid.*' => 'exists:murid,id'
        ]);

        $insertData = [];
        foreach ($request->id_murid as $id) {
            $insertData[] = [
                'id_kelas' => $request->id_kelas,
                'id_murid' => $id,
                'created_at' => now()
            ];
        }

        DB::table('murid_kelas')->insert($insertData);

        return redirect()->back()->with('success', 'Murid berhasil dimasukkan ke kelas!');
    }

    public function removeStudent($id_murid)
    {
        DB::table('murid_kelas')->where('id_murid', $id_murid)->delete();
        return redirect()->back()->with('success', 'Murid telah dikeluarkan dari kelas.');
    }

    public function destroy($id)
    {
        Kelas::where('uuid', $id)->firstOrFail()->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');
    }

    public function setWaliKelas(Request $request, $id)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id',
        ]);

        $kelas = Kelas::where('uuid', $id)->firstOrFail();
        $kelas->update(['id_wali_kelas' => $request->id_guru]);

        return redirect()->back()->with('success', 'Wali kelas berhasil ditetapkan!');
    }

    public function removeWaliKelas($id)
    {
        $kelas = Kelas::where('uuid', $id)->firstOrFail();
        $kelas->update(['id_wali_kelas' => null]);

        return redirect()->back()->with('success', 'Wali kelas berhasil dihapus.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ]);

        $kelas = Kelas::where('uuid', $id)->firstOrFail();
        $kelas->update([
            'nama_kelas' => $request->nama_kelas
        ]);

        return redirect()->route('kelas.index')->with('success', 'Nama kelas berhasil diperbarui!');
    }
}