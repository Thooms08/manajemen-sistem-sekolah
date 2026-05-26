<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeaktifanAdminController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil semua kelas untuk dropdown filter
        $kelas = DB::table('kelas')->get();

        // Menangkap input filter
        $kelasId = $request->get('id_kelas');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $dataKeaktifan = [];

        // Query hanya dijalankan jika admin sudah memilih kelas
        if ($kelasId) {
            $dataKeaktifan = DB::table('keaktifan_murid')
                ->join('keaktifans', 'keaktifan_murid.keaktifan_id', '=', 'keaktifans.id')
                ->join('murid', 'keaktifan_murid.murid_id', '=', 'murid.id')
                ->join('kelas', 'keaktifans.id_kelas', '=', 'kelas.id')
                ->select(
                    'murid.nama_lengkap',
                    'keaktifans.nama_keaktifan',
                    'keaktifans.tanggal',
                    'keaktifans.foto',
                    'keaktifan_murid.is_active'
                )
                ->where('keaktifans.id_kelas', $kelasId)
                ->whereDate('keaktifans.tanggal', $tanggal)
                ->get();
        }

        return view('dashboard_admin.keaktifan_murid', compact('kelas', 'dataKeaktifan', 'kelasId', 'tanggal'));
    }
}