<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumentasi;
use App\Models\ProgramSekolah;
use App\Models\Prestasi;
use App\Models\FotoPrestasi;
use App\Models\Artikel;
use App\Models\FotoArtikel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class InformasiController extends Controller
{
    public function index()
    {
        $kegiatan = Dokumentasi::all();
        $programs = ProgramSekolah::all();
        $prestasi = Prestasi::with('fotos')->get();
        $artikels = Artikel::with('fotos')->get();

        return view('dashboard_admin.informasi', compact('kegiatan', 'programs', 'prestasi', 'artikels'));
    }

    // --- KEGIATAN ---
    public function storeKegiatan(Request $request)
    {
        $request->validate([
            'label_foto' => 'required|string|max:255',
            'foto_kegiatan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi_foto' => 'nullable|string'
        ]);

        $data = [
        'label_foto' => $request->label_foto,
        'deskripsi_foto' => $request->deskripsi_foto ?? '-', 
            ];
        
        if ($request->hasFile('foto_kegiatan')) {
            $fileName = time() . '_kegiatan.' . $request->foto_kegiatan->extension();
            $request->foto_kegiatan->move(public_path('assets/kegiatan'), $fileName);
            $data['foto_kegiatan'] = 'assets/kegiatan/' . $fileName;
        }

        Dokumentasi::create($data);
        return redirect()->back()->with('success', 'Kegiatan berhasil disimpan');
    }

    public function destroyKegiatan($id)
    {
        $data = Dokumentasi::findOrFail($id);
        if (File::exists(public_path($data->foto_kegiatan))) File::delete(public_path($data->foto_kegiatan));
        $data->delete();
        return redirect()->back()->with('success', 'Kegiatan dihapus');
    }

    // --- PROGRAM SEKOLAH ---
    public function storeProgram(Request $request)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
            'deskripsi_program' => 'nullable|max:150',
        ]);

        ProgramSekolah::create($request->all());
        return redirect()->back()->with('success', 'Program berhasil disimpan');
    }

    public function destroyProgram($id)
    {
        ProgramSekolah::destroy($id);
        return redirect()->back()->with('success', 'Program dihapus');
    }

    // --- PRESTASI ---
    // Simpan Prestasi Baru
public function storePrestasi(Request $request)
{
    $request->validate([
        'judul_prestasi' => 'required|string|max:255',
        'deskripsi_prestasi' => 'nullable|string', // Tambahkan validasi ini
        'foto_prestasi.*' => 'image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // Berikan nilai default '-' jika deskripsi kosong agar database tidak error
    $dataPrestasi = [
        'judul_prestasi' => $request->judul_prestasi,
        'deskripsi_prestasi' => $request->deskripsi_prestasi ?? '-', 
    ];

    // Simpan data teks ke tabel prestasi
    $prestasi = Prestasi::create($dataPrestasi);

    // Simpan foto ke tabel foto_prestasi
    if ($request->hasFile('foto_prestasi')) {
        foreach ($request->file('foto_prestasi') as $file) {
            $name = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/kegiatan'), $name);
            
            FotoPrestasi::create([
                'id_prestasi' => $prestasi->id,
                'foto' => 'assets/kegiatan/' . $name
            ]);
        }
    }

    return redirect()->back()->with('success', 'Prestasi dan Foto berhasil disimpan');
}
    // --- ARTIKEL ---
    public function storeArtikel(Request $request)
{
    $request->validate([
        'judul_artikel' => 'required', 
        'foto_artikel.*' => 'image|mimes:jpg,jpeg,png|max:2048'
    ]);

    DB::transaction(function () use ($request) {
        // PEMETAAN MANUAL: 'judul' (DB) <- 'judul_artikel' (Form)
        $artikel = Artikel::create([
            'judul' => $request->judul_artikel,
            'deskripsi' => $request->deskripsi,
            'teaser' => $request->teaser
        ]);

        if ($request->hasFile('foto_artikel')) {
            foreach ($request->file('foto_artikel') as $key => $file) {
                $name = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('assets/artikel'), $name);
                
                FotoArtikel::create([
                    'id_artikel' => $artikel->id,
                    'foto_artikel' => 'assets/artikel/' . $name,
                    'sumber_foto' => $request->sumber_foto[$key] ?? '-'
                ]);
            }
        }
    });

    return redirect()->back()->with('success', 'Artikel berhasil disimpan');
}
    public function destroyArtikel($id)
    {
        $artikel = Artikel::with('fotos')->findOrFail($id);
        foreach ($artikel->fotos as $f) {
            if (File::exists(public_path($f->foto_artikel))) File::delete(public_path($f->foto_artikel));
        }
        $artikel->delete();
        return redirect()->back()->with('success', 'Artikel dihapus');
    }
    // --- UPDATE KEGIATAN ---
public function updateKegiatan(Request $request, $id) {
    $data = Dokumentasi::findOrFail($id);
    $request->validate(['label_foto' => 'required']);
    
    $updateData = $request->only(['label_foto', 'deskripsi_foto']);
    if ($request->hasFile('foto_kegiatan')) {
        if (File::exists(public_path($data->foto_kegiatan))) File::delete(public_path($data->foto_kegiatan));
        $fileName = time() . '_kegiatan.' . $request->foto_kegiatan->extension();
        $request->foto_kegiatan->move(public_path('assets/kegiatan'), $fileName);
        $updateData['foto_kegiatan'] = 'assets/kegiatan/' . $fileName;
    }
    $data->update($updateData);
    return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui');
}

// --- UPDATE PROGRAM ---
public function updateProgram(Request $request, $id) {
    $program = ProgramSekolah::findOrFail($id);
    $program->update($request->all());
    return redirect()->back()->with('success', 'Program berhasil diperbarui');
}

// --- UPDATE PRESTASI ---
public function updatePrestasi(Request $request, $id)
{
    $request->validate([
        'judul_prestasi' => 'required',
        'foto_prestasi.*' => 'image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $prestasi = Prestasi::findOrFail($id);

    // Update data teks
    $prestasi->update([
        'judul_prestasi' => $request->judul_prestasi,
        'deskripsi_prestasi' => $request->deskripsi_prestasi ?? '-',
    ]);

    // Jika admin mengunggah foto baru, tambahkan ke koleksi foto yang sudah ada
    if ($request->hasFile('foto_prestasi')) {
        foreach ($request->file('foto_prestasi') as $file) {
            $name = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/kegiatan'), $name);
            
            FotoPrestasi::create([
                'id_prestasi' => $prestasi->id,
                'foto' => 'assets/kegiatan/' . $name
            ]);
        }
    }

    return redirect()->back()->with('success', 'Data prestasi berhasil diperbarui');
}

// Hapus satu foto tertentu di dalam modal edit
public function destroyFotoPrestasi($id)
{
    $foto = FotoPrestasi::findOrFail($id);

    // Hapus file fisik
    if (File::exists(public_path($foto->foto))) {
        File::delete(public_path($foto->foto));
    }

    $foto->delete();

    return response()->json(['success' => 'Foto berhasil dihapus']);
}

// Update Artikel
// Update data teks dan tambah foto baru
public function updateArtikel(Request $request, $id) {
    $artikel = Artikel::findOrFail($id);
    $artikel->update([
        'judul' => $request->judul_artikel, 
        'teaser' => $request->teaser,
        'deskripsi' => $request->deskripsi
    ]);

    if ($request->hasFile('foto_artikel')) {
        foreach ($request->file('foto_artikel') as $key => $file) {
            $name = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/artikel'), $name);
            FotoArtikel::create([
                'id_artikel' => $artikel->id,
                'foto_artikel' => 'assets/artikel/' . $name,
                'sumber_foto' => $request->sumber_foto[$key] ?? '-'
            ]);
        }
    }
    return redirect()->back()->with('success', 'Artikel diperbarui');
}

// Hapus satu foto artikel dari server & DB
public function destroyFotoArtikel($id) {
    $foto = FotoArtikel::findOrFail($id);
    if (File::exists(public_path($foto->foto_artikel))) {
        File::delete(public_path($foto->foto_artikel));
    }
    $foto->delete();
    return response()->json(['success' => true]);
}
}