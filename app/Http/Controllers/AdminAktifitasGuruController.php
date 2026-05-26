<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAktifitasGuruController extends Controller
{
    public function index()
    {
        return view('dashboard_admin.aktifitas_guru');
    }

    public function getChartData(Request $request)
    {
        $range = $request->get('range', '1month');
        $now = Carbon::now();

        // Tentukan range hari
        if ($range == '1week') {
            $startDate = $now->copy()->subDays(6);
            $totalDays = 7;
        } elseif ($range == '1year') {
            $startDate = $now->copy()->subYear();
            $totalDays = 365;
        } else {
            $startDate = $now->copy()->subDays(29);
            $totalDays = 30;
        }

        $endDate = $now;

        // Ambil semua guru
       $gurus = DB::table('guru')->select('id', 'nama_guru')->get();

    $labels = []; $percentages = []; $colors = [];

    foreach ($gurus as $guru) {
        // Hitung berdasarkan guru.id
        $attendanceDays = DB::table('absensi_murid')
            ->where('id_guru', $guru->id) 
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->distinct('tanggal')->count();

        $activityDays = DB::table('keaktifans')
            ->where('id_guru', $guru->id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->distinct('tanggal')->count();

        $percentage = ($totalDays > 0) ? round((($attendanceDays + $activityDays) / 2 / $totalDays) * 100, 1) : 0;
        
        $labels[] = $guru->nama_guru;
        $percentages[] = ($percentage > 100) ? 100 : $percentage;
        $colors[] = $percentage >= 80 ? '#198754' : ($percentage >= 50 ? '#ffc107' : '#dc3545');
    }

    return response()->json([
        'labels' => $labels,
        'datasets' => [['data' => $percentages, 'backgroundColor' => $colors]]
    ]);
}
}