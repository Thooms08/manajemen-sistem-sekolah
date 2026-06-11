<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMaster\Kelas;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\Artikel;
use App\Models\Informasi\ProgramSekolah;
use App\Models\Informasi\Dokumentasi;
use App\Models\Informasi\InfoSekolah;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\Guru;
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