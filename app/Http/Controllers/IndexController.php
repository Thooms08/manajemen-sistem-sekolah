<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas; // Jika dibutuhkan
use App\Models\Prestasi;
use App\Models\Artikel;
use App\Models\ProgramSekolah;
use App\Models\Dokumentasi;
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
        $prestasi = Prestasi::with('fotos')->latest()->get();

        // 4. Ambil program sekolah
        $programs = ProgramSekolah::all();

        // 5. Ambil 3 artikel terbaru beserta relasi fotonya
        $artikels = Artikel::with('fotos')->latest()->take(3)->get();

        return view('index.index', compact(
            'sekolah', 
            'kegiatan', 
            'prestasi', 
            'programs', 
            'artikels'
        ));
    }

    public function showArtikel($id)
    {
        $artikel = Artikel::with('fotos')->findOrFail($id);
        $sekolah = DB::table('profile_sekolah')->first();
        return view('index.detail_artikel', compact('artikel', 'sekolah'));
    }
}