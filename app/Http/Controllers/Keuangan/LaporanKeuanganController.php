<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\Pemasukan;
use App\Models\Keuangan\Pengeluaran;
use App\Models\Informasi\ProfileSekolah;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class LaporanKeuanganController extends Controller
{
    /**
     * Hitung rentang tanggal berdasarkan periode yang dipilih.
     * Return: [ Carbon $from, Carbon $to, string $periode ]
     */
    private function resolveRange(Request $request): array
    {
        $periode  = $request->get('periode', '6bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if ($dateFrom && $dateTo) {
            return [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
                'custom',
            ];
        }

        $to = Carbon::now()->endOfDay();
        $from = match ($periode) {
            '1bulan'  => Carbon::now()->subMonth()->startOfDay(),
            '3bulan'  => Carbon::now()->subMonths(3)->startOfDay(),
            '6bulan'  => Carbon::now()->subMonths(6)->startOfDay(),
            '1tahun'  => Carbon::now()->subYear()->startOfDay(),
            'tahunini'=> Carbon::now()->startOfYear(),
            default   => Carbon::now()->subMonths(6)->startOfDay(),
        };

        return [$from, $to, $periode];
    }

    /**
     * Halaman utama laporan keuangan.
     */
    public function index(Request $request)
    {
        $sekolah = ProfileSekolah::first();

        [$from, $to, $periode] = $this->resolveRange($request);
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        // ── 1. SUMMARY CARDS ──────────────────────────────────────────
        $totalPemasukan   = Pemasukan::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');

        $totalPengeluaran = Pengeluaran::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');

        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        $jumlahTxPemasukan   = Pemasukan::tersedia()->whereBetween('created_at', [$from, $to])->count();
        $jumlahTxPengeluaran = Pengeluaran::tersedia()->whereBetween('created_at', [$from, $to])->count();

        // ── 2. TREN BULANAN (untuk Line/Bar Chart) ────────────────────
        // Buat daftar bulan dalam rentang
        $bulanList = [];
        $cur = $from->copy()->startOfMonth();
        while ($cur->lte($to)) {
            $bulanList[] = $cur->copy();
            $cur->addMonth();
        }

        // Aggregate pemasukan per bulan
        $pemasukanBulanan = Pemasukan::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // Aggregate pengeluaran per bulan
        $pengeluaranBulanan = Pengeluaran::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // Bangun array berurutan untuk chart
        $chartLabels        = [];
        $chartPemasukan     = [];
        $chartPengeluaran   = [];
        $chartSaldo         = [];

        foreach ($bulanList as $bulan) {
            $key = $bulan->format('Y-m');
            $p   = (int) ($pemasukanBulanan[$key]   ?? 0);
            $e   = (int) ($pengeluaranBulanan[$key] ?? 0);
            $chartLabels[]      = $bulan->translatedFormat('M Y');
            $chartPemasukan[]   = $p;
            $chartPengeluaran[] = $e;
            $chartSaldo[]       = $p - $e;
        }

        // ── 3. KOMPOSISI PENGELUARAN (untuk Doughnut) ─────────────────
        $komposiPengeluaran = Pengeluaran::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("jenis_pengeluaran, SUM(total) as total")
            ->groupBy('jenis_pengeluaran')
            ->pluck('total', 'jenis_pengeluaran');

        $jenisMap = [
            'operasional' => 'Operasional',
            'gaji_staff'  => 'Gaji Staff',
            'gaji_guru'   => 'Gaji Guru',
            'lainnya'     => 'Lainnya',
        ];
        $donutLabels = [];
        $donutData   = [];
        foreach ($jenisMap as $key => $label) {
            $donutLabels[] = $label;
            $donutData[]   = (int) ($komposiPengeluaran[$key] ?? 0);
        }

        // ── 4. KOMPOSISI PEMASUKAN (untuk Bar horizontal) ─────────────
        $komposiPemasukan = Pemasukan::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("jenis_pemasukan, SUM(total) as total")
            ->groupBy('jenis_pemasukan')
            ->pluck('total', 'jenis_pemasukan');

        $jenisPemasukanMap = [
            'biaya_ppdb'     => 'Biaya PPDB',
            'donasi'         => 'Donasi',
            'bantuan_sosial' => 'Bantuan Sosial',
            'lainnya'        => 'Lainnya',
        ];
        $barPemasukanLabels = [];
        $barPemasukanData   = [];
        foreach ($jenisPemasukanMap as $key => $label) {
            $barPemasukanLabels[] = $label;
            $barPemasukanData[]   = (int) ($komposiPemasukan[$key] ?? 0);
        }

        // ── 5. TABEL LAPORAN BULANAN ──────────────────────────────────
        $tabelBulanan = [];
        foreach ($bulanList as $bulan) {
            $key  = $bulan->format('Y-m');
            $p    = (int) ($pemasukanBulanan[$key]   ?? 0);
            $e    = (int) ($pengeluaranBulanan[$key] ?? 0);
            $saldo = $p - $e;
            $tabelBulanan[] = [
                'bulan'       => $bulan->translatedFormat('F Y'),
                'pemasukan'   => $p,
                'pengeluaran' => $e,
                'saldo'       => $saldo,
                'status'      => $saldo >= 0 ? 'surplus' : 'defisit',
            ];
        }

        // ── 6. TRANSAKSI TERAKHIR (5 pemasukan + 5 pengeluaran) ───────
        $txPemasukan   = Pemasukan::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->latest()->limit(5)->get();

        $txPengeluaran = Pengeluaran::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->latest()->limit(5)->get();

        return view('admin.keuangan.laporan', compact(
            'sekolah', 'periode', 'dateFrom', 'dateTo',
            'from', 'to',
            // Summary
            'totalPemasukan', 'totalPengeluaran', 'saldoBersih',
            'jumlahTxPemasukan', 'jumlahTxPengeluaran',
            // Chart tren
            'chartLabels', 'chartPemasukan', 'chartPengeluaran', 'chartSaldo',
            // Chart donut pengeluaran
            'donutLabels', 'donutData',
            // Chart bar pemasukan
            'barPemasukanLabels', 'barPemasukanData',
            // Tabel bulanan
            'tabelBulanan',
            // Transaksi terakhir
            'txPemasukan', 'txPengeluaran'
        ));
    }

    /**
     * Export Excel laporan periode terpilih.
     */
    public function exportExcel(Request $request)
    {
        [$from, $to, $periode] = $this->resolveRange($request);

        // Buat daftar bulan
        $bulanList = [];
        $cur = $from->copy()->startOfMonth();
        while ($cur->lte($to)) {
            $bulanList[] = $cur->copy();
            $cur->addMonth();
        }

        $pemasukanBulanan = Pemasukan::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total) as total")
            ->groupBy('bulan')->pluck('total', 'bulan');

        $pengeluaranBulanan = Pengeluaran::tersedia()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total) as total")
            ->groupBy('bulan')->pluck('total', 'bulan');

        $rows = collect();
        foreach ($bulanList as $bulan) {
            $key  = $bulan->format('Y-m');
            $p    = (int) ($pemasukanBulanan[$key]   ?? 0);
            $e    = (int) ($pengeluaranBulanan[$key] ?? 0);
            $rows->push([
                'Bulan'                => $bulan->translatedFormat('F Y'),
                'Total Pemasukan (Rp)' => $p,
                'Total Pengeluaran (Rp)' => $e,
                'Saldo (Rp)'           => $p - $e,
                'Status'               => ($p - $e) >= 0 ? 'Surplus' : 'Defisit',
            ]);
        }

        $filename = 'laporan_keuangan_' . $from->format('Ymd') . '_sd_' . $to->format('Ymd') . '.xlsx';
        return (new FastExcel($rows))->download($filename);
    }
}
