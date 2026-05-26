<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $isMinggu = Carbon::now()->isSunday();
        $kelas = DB::table('kelas')->get();
        return view('dashboard_guru.absensi', compact('kelas', 'isMinggu'));
    }

    public function store(Request $request)
{
    $request->validate(['id_kelas' => 'required', 'absensi' => 'required|array']);

    if (Carbon::now()->isSunday()) {
        return redirect()->back()->with('error', 'Tidak dapat melakukan absensi di hari Minggu.');
    }

    // CARI id guru dari tabel guru
    $guru = DB::table('guru')->where('id_user', Auth::id())->first();
    
    if (!$guru) {
        return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
    }

    try {
        DB::transaction(function () use ($request, $guru) {
            foreach ($request->absensi as $id_murid => $data) {
                DB::table('absensi_murid')->updateOrInsert(
                    ['id_murid' => $id_murid, 'tanggal' => Carbon::now()->format('Y-m-d')],
                    [
                        'id_guru'    => $guru->id, // Menggunakan ID dari tabel guru
                        'status'     => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );
            }
        });
        return redirect()->back()->with('success', 'Absensi berhasil disimpan.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    public function getArsip(Request $request)
{
    $id_kelas = $request->id_kelas;
    $search = $request->search;
    $bulan = $request->bulan ?? date('m');
    $tahun = $request->tahun ?? date('Y');

    // Query dasar: Ambil murid berdasarkan kelas
    $query = DB::table('murid_kelas')
        ->join('murid', 'murid_kelas.id_murid', '=', 'murid.id')
        // Left Join ke tabel absensi untuk menghitung jumlah status
        ->leftJoin('absensi_murid', function($join) use ($bulan, $tahun) {
            $join->on('murid.id', '=', 'absensi_murid.id_murid')
                 ->whereMonth('absensi_murid.tanggal', $bulan)
                 ->whereYear('absensi_murid.tanggal', $tahun);
        })
        ->where('murid_kelas.id_kelas', $id_kelas);

    // Filter pencarian nama
    if (!empty($search)) {
        $query->where('murid.nama_lengkap', 'like', '%' . $search . '%');
    }

    $murid = $query->select(
            'murid.id', 
            'murid.nama_lengkap',
            // Menghitung jumlah hadir & tidak hadir menggunakan SQL CASE
            DB::raw("SUM(CASE WHEN absensi_murid.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir"),
            DB::raw("SUM(CASE WHEN absensi_murid.status = 'tidak_hadir' THEN 1 ELSE 0 END) as total_tidak_hadir")
        )
        ->groupBy('murid.id', 'murid.nama_lengkap')
        ->get();

    return response()->json($murid);
}
public function getRekapMurid(Request $request)
{
    // 1. Pastikan input adalah integer untuk mencegah error pada Carbon
    $bulan = (int) ($request->bulan ?? date('m'));
    $tahun = (int) ($request->tahun ?? date('Y'));
    $id_murid = $request->id_murid;

    if (!$id_murid) {
        return response()->json(['error' => 'ID Murid tidak ditemukan'], 400);
    }

    try {
        // 2. Gunakan createFromDate agar lebih aman dari resiko 'month overflow'
        // (Misal: hari ini tgl 31, lalu membuat Carbon Feb, maka akan loncat ke Maret)
        $dateContext = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $dateContext->daysInMonth;
        
        // 3. Ambil data absensi
        $absensi = DB::table('absensi_murid')
            ->where('id_murid', $id_murid)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->keyBy(function($item) {
                // Pastikan kolom 'tanggal' di database tidak null sebelum di-parse
                return (int)Carbon::parse($item->tanggal)->format('d');
            });

        $rekap = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            // 4. Buat objek tanggal untuk pengecekan hari Minggu
            $checkDate = Carbon::createFromDate($tahun, $bulan, $i);
            
            if ($checkDate->isSunday()) {
                $status = 'libur';
            } else {
                // 5. Cek apakah ada data di database untuk tanggal ke-$i
                // Jika tidak ada data di DB, status adalah 'none' (abu-abu)
                $status = isset($absensi[$i]) ? $absensi[$i]->status : 'none';
            }
            
            $rekap[] = [
                'tgl' => $i,
                'status' => $status
            ];
        }

        return response()->json($rekap);

    } catch (\Exception $e) {
        // 6. Jika terjadi error, kirim pesan spesifik agar bisa dibaca di Network Tab
        return response()->json([
            'error' => 'Terjadi kesalahan pada server',
            'debug' => $e->getMessage()
        ], 500);
    }
}
}