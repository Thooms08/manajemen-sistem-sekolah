<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Murid;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
{
    // Mengambil semua kelas dengan jumlah muridnya
    $kelas = Kelas::withCount('murid')->get();

    // Mengambil murid yang BELUM memiliki kelas (agar tidak error di blade)
    $muridTersedia = \App\Models\Murid::whereNotExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('murid_kelas')
              ->whereRaw('murid_kelas.id_murid = murid.id');
    })->get();

    return view('dashboard_admin.kelas', compact('kelas', 'muridTersedia'));
}

    // HALAMAN BARU: Menampilkan detail kelas dan daftar murid di dalamnya
    public function show($id)
    {
        $kelas = Kelas::with('murid')->findOrFail($id);
        
        // Ambil murid yang belum punya kelas untuk pilihan "Tambah Murid"
        $muridTersedia = Murid::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('murid_kelas')
                  ->whereRaw('murid_kelas.id_murid = murid.id');
        })->get();

        return view('dashboard_admin.detail_kelas', compact('kelas', 'muridTersedia'));
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
            'id_murid' => 'required|exists:murid,id'
        ]);

        DB::table('murid_kelas')->insert([
            'id_kelas' => $request->id_kelas,
            'id_murid' => $request->id_murid,
            'created_at' => now()
        ]);

        return redirect()->back()->with('success', 'Murid berhasil dimasukkan ke kelas!');
    }

    public function removeStudent($id_murid)
    {
        DB::table('murid_kelas')->where('id_murid', $id_murid)->delete();
        return redirect()->back()->with('success', 'Murid telah dikeluarkan dari kelas.');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'nama_kelas' => 'required|string|max:255',
    ]);

    $kelas = Kelas::findOrFail($id);
    $kelas->update([
        'nama_kelas' => $request->nama_kelas
    ]);

    return redirect()->route('kelas.index')->with('success', 'Nama kelas berhasil diperbarui!');
}
}