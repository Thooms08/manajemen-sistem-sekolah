<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAbsensiController extends Controller
{
    public function index()
    {
        $kelas = DB::table('kelas')->get();
        return view('dashboard_admin.arsip_absen', compact('kelas'));
    }

    public function getMurid(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $search = $request->search;

        $query = DB::table('murid_kelas')
            ->join('murid', 'murid_kelas.id_murid', '=', 'murid.id')
            ->leftJoin('absensi_murid', function($join) use ($bulan, $tahun) {
                $join->on('murid.id', '=', 'absensi_murid.id_murid')
                     ->whereMonth('absensi_murid.tanggal', $bulan)
                     ->whereYear('absensi_murid.tanggal', $tahun);
            })
            ->where('murid_kelas.id_kelas', $id_kelas);

        if (!empty($search)) {
            $query->where('murid.nama_lengkap', 'like', '%' . $search . '%');
        }

        $data = $query->select(
            'murid.id',
            'murid.nama_lengkap',
            'murid.nisn',
            DB::raw("SUM(CASE WHEN absensi_murid.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir"),
            DB::raw("SUM(CASE WHEN absensi_murid.status = 'tidak_hadir' THEN 1 ELSE 0 END) as total_alfa")
        )
        ->groupBy('murid.id', 'murid.nama_lengkap', 'murid.nisn')
        ->get();

        return response()->json($data);
    }

    public function getRekapIndividu(Request $request)
    {
        $id_murid = $request->id_murid;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $absensi = DB::table('absensi_murid')
            ->where('id_murid', $id_murid)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy(function($item) {
                return (int)Carbon::parse($item->tanggal)->format('d');
            });

        $calendar = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($tahun, $bulan, $i);
            if ($date->isSunday()) {
                $status = 'libur';
            } else {
                $status = isset($absensi[$i]) ? $absensi[$i]->status : 'none';
            }
            $calendar[] = ['tgl' => $i, 'status' => $status];
        }

        return response()->json($calendar);
    }
}