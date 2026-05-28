<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Prestasi;
use App\Models\Artikel;
use App\Models\ProgramSekolah;
use App\Models\Dokumentasi;
use App\Models\InfoSekolah;
use App\Models\Murid;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        // 1. Ambil data profil sekolah (asumsi hanya ada 1 baris data)
        $sekolah = DB::table('profile_sekolah')->first();

        // 2. Ambil data dokumentasi untuk Slider Hero
        $kegiatan = Dokumentasi::all();

        // 3. Ambil prestasi beserta relasi fotonya
        $prestasi = Prestasi::all();

        // 4. Ambil program sekolah
        $programs = ProgramSekolah::all();

        // 5. Ambil 3 artikel terbaru beserta relasi fotonya
        $artikels = Artikel::all()->take(3);

        // 6. Ambil Data Informasi Tambahan & Statistik
        $infoSekolah = InfoSekolah::first();
        $jumlah_kelas = Kelas::count();
        $jumlah_murid = Murid::where('status', 'konfirmasi')->count();
        $jumlah_guru_db = Guru::count();

        return view('index.index', compact(
            'sekolah', 
            'kegiatan', 
            'prestasi', 
            'programs', 
            'artikels',
            'infoSekolah',
            'jumlah_kelas',
            'jumlah_murid',
            'jumlah_guru_db'
        ));
    }

    public function showArtikel($id)
    {
        $artikel = Artikel::with('fotos')->findOrFail($id);
        $sekolah = DB::table('profile_sekolah')->first();
        return view('index.detail_artikel', compact('artikel', 'sekolah'));
    }
}