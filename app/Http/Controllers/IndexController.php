<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMaster\Kelas;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\Artikel;
use App\Models\Informasi\ProgramSekolah;
use App\Models\Informasi\Dokumentasi;
use App\Models\Informasi\InfoSekolah;
use App\Models\Informasi\StudiSekolah;
use App\Models\Informasi\Brosur;
use App\Models\DataMaster\Murid;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Staff;
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
        $infoSekolah    = InfoSekolah::first();
        $jumlah_kelas   = Kelas::count();
        $jumlah_murid   = Murid::where('status', 'konfirmasi')->count();
        $jumlah_guru_db = Guru::count();
        $jumlah_staff   = Staff::count();
        $studiList      = StudiSekolah::orderBy('id')->get();

        // Jumlah guru: SELALU prioritaskan hitungan nyata dari tabel guru.
        // Hanya fallback ke angka di InfoSekolah jika tabel guru benar-benar kosong.
        $jumlah_guru_tampil = $jumlah_guru_db > 0
            ? $jumlah_guru_db
            : ($infoSekolah->jumlah_guru ?? 0);

        // Jumlah staff: sama, prioritaskan tabel staff.
        $jumlah_staff_tampil = $jumlah_staff > 0
            ? $jumlah_staff
            : ($infoSekolah->jumlah_staff ?? 0);

        // Section informasi muncul jika ada salah satu data ini (tidak harus ada infoSekolah)
        $adaDataInformasi = $jumlah_guru_tampil > 0
            || $jumlah_staff_tampil > 0
            || $jumlah_murid > 0
            || $jumlah_kelas > 0
            || ($infoSekolah && ($infoSekolah->nama_kepala_sekolah || $infoSekolah->fasilitas))
            || $studiList->isNotEmpty();

        return view('index.index', compact(
            'sekolah',
            'kegiatan',
            'prestasi',
            'programs',
            'artikels',
            'infoSekolah',
            'jumlah_kelas',
            'jumlah_murid',
            'jumlah_guru_db',
            'jumlah_guru_tampil',
            'jumlah_staff_tampil',
            'adaDataInformasi',
            'studiList'
        ));
    }

    public function showArtikel($id)
    {
        $artikel = Artikel::with('fotos')->findOrFail($id);
        $sekolah = DB::table('profile_sekolah')->first();
        return view('index.detail_artikel', compact('artikel', 'sekolah'));
    }

    public function brosur()
    {
        $sekolah    = DB::table('profile_sekolah')->first();
        $brosurList = Brosur::latest()->get();
        return view('index.brosur', compact('sekolah', 'brosurList'));
    }
}