<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan\Role;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;
use App\Models\DataMaster\Kelas;
use App\Models\Keuangan\Pemasukan;
use App\Models\Keuangan\Pengeluaran;
use App\Models\Informasi\Artikel;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\Dokumentasi;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $roleSlug = $user->role ?? $user->rules ?? '';

        $role = Role::with('permissions')->where('slug', $roleSlug)->first();
        if (!$role) {
            abort(403, 'Role tidak ditemukan. Hubungi Administrator.');
        }

        // ── Data statistik (sama seperti dashboard admin) ──────────────────
        $totalMuridAktif           = Murid::where('status', 'konfirmasi')->count();
        $totalMuridPending         = Murid::where('status', 'pending')->count();
        $totalGuruAktif            = Guru::where('status', 'aktif')->count();
        $totalStaffAktif           = Staff::where('status', 'aktif')->count();
        $totalKelas                = Kelas::count();
        $bulanIni                  = now()->month;
        $tahunIni                  = now()->year;
        $totalPemasukanBulanIni    = Pemasukan::tersedia()->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->sum('total');
        $totalPengeluaranBulanIni  = Pengeluaran::tersedia()->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->sum('total');
        $totalPemasukan            = Pemasukan::tersedia()->sum('total');
        $totalPengeluaran          = Pengeluaran::tersedia()->sum('total');
        $saldo                     = $totalPemasukan - $totalPengeluaran;
        $totalArtikel              = Artikel::count();
        $totalPrestasi             = Prestasi::count();
        $totalKegiatan             = Dokumentasi::count();
        $artikelTerbaru            = Artikel::latest()->take(3)->get();
        $muridTerbaru              = Murid::whereIn('status', ['konfirmasi', 'pending'])->latest()->take(5)->get(['nama_lengkap', 'status', 'created_at', 'uuid']);
        $grafik = collect(range(5, 0))->map(function ($bulanLalu) {
            $tanggal     = now()->subMonths($bulanLalu);
            $bulan       = $tanggal->month;
            $tahun       = $tanggal->year;
            $pemasukan   = Pemasukan::tersedia()->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('total');
            $pengeluaran = Pengeluaran::tersedia()->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->sum('total');
            return ['label' => $tanggal->isoFormat('MMM YY'), 'pemasukan' => $pemasukan, 'pengeluaran' => $pengeluaran];
        });

        return view('user.index', compact(
            'role',
            'totalMuridAktif', 'totalMuridPending', 'totalGuruAktif', 'totalStaffAktif', 'totalKelas',
            'totalPemasukanBulanIni', 'totalPengeluaranBulanIni',
            'totalPemasukan', 'totalPengeluaran', 'saldo',
            'totalArtikel', 'totalPrestasi', 'totalKegiatan',
            'artikelTerbaru', 'muridTerbaru', 'grafik'
        ));
    }
}
