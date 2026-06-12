<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;
use App\Models\DataMaster\Kelas;
use App\Models\Keuangan\Pemasukan;
use App\Models\Keuangan\Pengeluaran;
use App\Models\Informasi\Artikel;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\Dokumentasi;
use App\Models\PpdbDraft;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Data Master ───────────────────────────────────────────────────────
        $totalMuridAktif  = Murid::where('status', 'konfirmasi')->count();
        $totalMuridPending = Murid::where('status', 'pending')->count();
        $totalGuruAktif   = Guru::where('status', 'aktif')->count();
        $totalStaffAktif  = Staff::where('status', 'aktif')->count();
        $totalKelas       = Kelas::count();

        // ── Keuangan bulan ini ────────────────────────────────────────────────
        $bulanIni   = now()->month;
        $tahunIni   = now()->year;

        $totalPemasukanBulanIni = Pemasukan::tersedia()
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('total');

        $totalPengeluaranBulanIni = Pengeluaran::tersedia()
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('total');

        $totalPemasukan  = Pemasukan::tersedia()->sum('total');
        $totalPengeluaran = Pengeluaran::tersedia()->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // ── Konten Informasi ──────────────────────────────────────────────────
        $totalArtikel  = Artikel::count();
        $totalPrestasi = Prestasi::count();
        $totalKegiatan = Dokumentasi::count();

        // ── Artikel terbaru (3 terakhir) ──────────────────────────────────────
        $artikelTerbaru = Artikel::latest()->take(3)->get();

        // ── Aktivitas terbaru: 5 murid terdaftar paling baru ──────────────────
        $muridTerbaru = Murid::whereIn('status', ['konfirmasi', 'pending'])
            ->latest()
            ->take(5)
            ->get(['nama_lengkap', 'status', 'created_at', 'uuid']);

        // ── Grafik pemasukan 6 bulan terakhir ─────────────────────────────────
        $grafik = collect(range(5, 0))->map(function ($bulanLalu) {
            $tanggal    = now()->subMonths($bulanLalu);
            $bulan      = $tanggal->month;
            $tahun      = $tanggal->year;
            $pemasukan  = Pemasukan::tersedia()->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('total');
            $pengeluaran = Pengeluaran::tersedia()->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('total');

            return [
                'label'       => $tanggal->isoFormat('MMM YY'),
                'pemasukan'   => $pemasukan,
                'pengeluaran' => $pengeluaran,
            ];
        });

        return view('admin.index', compact(
            'totalMuridAktif',
            'totalMuridPending',
            'totalGuruAktif',
            'totalStaffAktif',
            'totalKelas',
            'totalPemasukanBulanIni',
            'totalPengeluaranBulanIni',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'totalArtikel',
            'totalPrestasi',
            'totalKegiatan',
            'artikelTerbaru',
            'muridTerbaru',
            'grafik'
        ));
    }
}
