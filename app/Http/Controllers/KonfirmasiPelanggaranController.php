<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiPelanggaranController extends Controller
{
    public function index()
    {
        // Mengambil data pelanggaran yang berstatus pending dengan join
        $pendingPelanggaran = DB::table('pelanggaran_murid')
            ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
            ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
            ->select(
                'pelanggaran_murid.*',
                'murid.nama_lengkap',
                'aturan_pelanggaran.nama_pelanggaran',
                'aturan_pelanggaran.skor'
            )
            ->where('pelanggaran_murid.status', 'pending')
            ->orderBy('pelanggaran_murid.created_at', 'desc')
            ->get();

        return view('dashboard_admin.konfirmasi_pelanggaran', compact('pendingPelanggaran'));
    }

    public function approve($id)
    {
        DB::table('pelanggaran_murid')
            ->where('id', $id)
            ->update([
                'status' => 'konfirmasi',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil dikonfirmasi.');
    }

    public function reject($id)
    {
        DB::table('pelanggaran_murid')
            ->where('id', $id)
            ->update([
                'status' => 'tolak',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Pelanggaran telah ditolak.');
    }
    // app/Http/Controllers/KonfirmasiPelanggaranController.php

public function getPendingCount()
{
    // Hitung data yang statusnya 'pending'
    $count = DB::table('pelanggaran_murid')
               ->where('status', 'pending')
               ->count();

    return response()->json(['count' => $count]);
}
}