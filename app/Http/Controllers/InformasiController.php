<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumentasi;
use App\Models\ProgramSekolah;
use App\Models\Prestasi;
use App\Models\Artikel;
use App\Models\StudiSekolah;
use App\Models\InfoSekolah;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    /**
     * Fungsi Helper Baru untuk Kompresi Gambar ke < 100KB menggunakan PHP GD
     */
    private function compressImage($sourcePath, $destinationPath)
    {
        $info = getimagesize($sourcePath);
        if (!$info) {
            copy($sourcePath, $destinationPath);
            return;
        }

        $mime = $info['mime'];
        $maxSize = 102400; // 100KB dalam Bytes

        if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
            $image = imagecreatefromjpeg($sourcePath);
            $quality = 90; // Set kualitas awal
            
            imagejpeg($image, $destinationPath, $quality);
            
            // Turunkan kualitas secara bertahap jika ukuran masih di atas 100KB
            while (file_exists($destinationPath) && filesize($destinationPath) > $maxSize && $quality > 10) {
                $quality -= 10;
                imagejpeg($image, $destinationPath, $quality);
            }
            imagedestroy($image);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($sourcePath);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            
            // Untuk PNG, GD menggunakan level kompresi 0-9 (9 paling maksimal)
            imagepng($image, $destinationPath, 9);
            imagedestroy($image);
        } else {
            // Fallback jika format gambar selain JPG/PNG
            copy($sourcePath, $destinationPath);
        }
    }

    public function index()
    {
        $kegiatan = Dokumentasi::all();
        $programs = ProgramSekolah::all();
        $prestasi = Prestasi::all();
        $artikels = Artikel::all();
        $studiList = StudiSekolah::all();
        $infoSekolah = InfoSekolah::first();

        return view('admin.informasi_sekolah.informasi', compact(
            'kegiatan', 'programs', 'prestasi', 'artikels', 'studiList', 'infoSekolah'
        ));
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
            $destinationPath = public_path('assets/kegiatan/' . $fileName);
            
            // Menggunakan fungsi compressImage
            $this->compressImage($request->file('foto_kegiatan')->getPathname(), $destinationPath);
            
            $data['foto_kegiatan'] = 'assets/kegiatan/' . $fileName;
        }

        Dokumentasi::create($data);
        return redirect()->back()->with('success', 'Kegiatan berhasil disimpan');
    }

    public function updateKegiatan(Request $request, $id)
    {
        $data = Dokumentasi::findOrFail($id);
        $request->validate(['label_foto' => 'required']);

        $updateData = $request->only(['label_foto', 'deskripsi_foto']);
        
        if ($request->hasFile('foto_kegiatan')) {
            if (File::exists(public_path($data->foto_kegiatan))) File::delete(public_path($data->foto_kegiatan));
            
            $fileName = time() . '_kegiatan.' . $request->foto_kegiatan->extension();
            $destinationPath = public_path('assets/kegiatan/' . $fileName);
            
            $this->compressImage($request->file('foto_kegiatan')->getPathname(), $destinationPath);
            
            $updateData['foto_kegiatan'] = 'assets/kegiatan/' . $fileName;
        }
        $data->update($updateData);
        return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui');
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

    public function updateProgram(Request $request, $id)
    {
        $program = ProgramSekolah::findOrFail($id);
        $program->update($request->all());
        return redirect()->back()->with('success', 'Program berhasil diperbarui');
    }

    public function destroyProgram($id)
    {
        ProgramSekolah::destroy($id);
        return redirect()->back()->with('success', 'Program dihapus');
    }

    public function storePrestasi(Request $request)
    {
        $request->validate([
            'judul_prestasi'    => 'required|string|max:255',
            'deskripsi_prestasi'=> 'nullable|string',
            'foto_prestasi'     => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = [
            'judul_prestasi'    => $request->judul_prestasi,
            'deskripsi_prestasi'=> $request->deskripsi_prestasi ?? '-',
            'foto_prestasi'     => '-',
        ];

        if ($request->hasFile('foto_prestasi')) {
            $file   = $request->file('foto_prestasi');
            $name   = time() . '_prestasi.' . $file->extension();
            $destinationPath = public_path('assets/prestasi/' . $name);
            
            $this->compressImage($file->getPathname(), $destinationPath);
            
            $data['foto_prestasi'] = 'assets/prestasi/' . $name;
        }

        Prestasi::create($data);
        return redirect()->back()->with('success', 'Data prestasi berhasil disimpan');
    }

    // --- PRESTASI (UPDATE) ---
    public function updatePrestasi(Request $request, $id)
    {
        $request->validate([
            'judul_prestasi'    => 'required|string|max:255',
            'deskripsi_prestasi'=> 'nullable|string',
            'foto_prestasi'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $prestasi   = Prestasi::findOrFail($id);
        $updateData = [
            'judul_prestasi'    => $request->judul_prestasi,
            'deskripsi_prestasi'=> $request->deskripsi_prestasi ?? '-',
        ];

        if ($request->hasFile('foto_prestasi')) {
            if ($prestasi->foto_prestasi && File::exists(public_path($prestasi->foto_prestasi))) {
                File::delete(public_path($prestasi->foto_prestasi));
            }
            $file = $request->file('foto_prestasi');
            $name = time() . '_prestasi.' . $file->extension();
            $destinationPath = public_path('assets/prestasi/' . $name);
            
            $this->compressImage($file->getPathname(), $destinationPath);
            
            $updateData['foto_prestasi'] = 'assets/prestasi/' . $name;
        }

        $prestasi->update($updateData);
        return redirect()->back()->with('success', 'Data prestasi berhasil diperbarui');
    }

    public function destroyPrestasi($id)
    {
        $prestasi = Prestasi::findOrFail($id);

        if ($prestasi->foto_prestasi && $prestasi->foto_prestasi !== '-') {
            if (File::exists(public_path($prestasi->foto_prestasi))) {
                File::delete(public_path($prestasi->foto_prestasi));
            }
        }

        $prestasi->delete();
        return redirect()->back()->with('success', 'Prestasi berhasil dihapus');
    }

    public function showArtikel($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        $sekolah = \App\Models\ProfileSekolah::first() ?? \App\Models\InfoSekolah::first() ?? InfoSekolah::first(); 
        $infoSekolah = InfoSekolah::first();

        return view('index.artikel', compact('artikel', 'sekolah', 'infoSekolah'));
    }

    public function storeArtikel(Request $request)
    {
        $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'foto_artikel'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_artikel')) {
            $file     = $request->file('foto_artikel');
            $name     = time() . '_artikel.' . $file->extension();
            $destinationPath = public_path('assets/artikel/' . $name);
            
            $this->compressImage($file->getPathname(), $destinationPath);
            
            $fotoPath = 'assets/artikel/' . $name;
        }

        // 2. LOGIKA GENERATE SLUG UNIK
        $slug = Str::slug($request->judul_artikel);
        if (Artikel::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . rand(100, 999);
        }

        Artikel::create([
            'judul'        => $request->judul_artikel,
            'slug'         => $slug, // <-- Simpan slug ke DB
            'deskripsi'    => $request->deskripsi,
            'teaser'       => $request->teaser,
            'foto_artikel' => $fotoPath,
        ]);

        return redirect()->back()->with('success', 'Artikel berhasil disimpan');
    }

    public function updateArtikel(Request $request, $id)
    {
        $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'foto_artikel'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $artikel    = Artikel::findOrFail($id);
        
        // 3. LOGIKA UPDATE SLUG UNIK (Jika judul berubah)
        $slug = Str::slug($request->judul_artikel);
        if (Artikel::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $slug . '-' . rand(100, 999);
        }

        $updateData = [
            'judul'     => $request->judul_artikel,
            'slug'      => $slug, // <-- Update slug baru
            'teaser'    => $request->teaser,
            'deskripsi' => $request->deskripsi,
        ];

        if ($request->hasFile('foto_artikel')) {
            if ($artikel->foto_artikel && File::exists(public_path($artikel->foto_artikel))) {
                File::delete(public_path($artikel->foto_artikel));
            }
            $file     = $request->file('foto_artikel');
            $name     = time() . '_artikel.' . $file->extension();
            $destinationPath = public_path('assets/artikel/' . $name);
            
            $this->compressImage($file->getPathname(), $destinationPath);
            
            $updateData['foto_artikel'] = 'assets/artikel/' . $name;
        }

        $artikel->update($updateData);
        return redirect()->back()->with('success', 'Artikel diperbarui');
    }

    // --- DESTROY ARTIKEL ---
    public function destroyArtikel($id)
    {
        $artikel = Artikel::findOrFail($id);
        if ($artikel->foto_artikel && File::exists(public_path($artikel->foto_artikel))) {
            File::delete(public_path($artikel->foto_artikel));
        }
        $artikel->delete();
        return redirect()->back()->with('success', 'Artikel dihapus');
    }

    // --- PROGRAM STUDI ---
    public function storeStudi(Request $request)
    {
        $request->validate([
            'nama_studi' => 'required|string|max:255',
            'deskripsi_studi' => 'nullable|string',
        ]);

        StudiSekolah::create($request->only(['nama_studi', 'deskripsi_studi']));
        return redirect()->back()->with('success', 'Program Studi berhasil disimpan');
    }

    public function updateStudi(Request $request, $id)
    {
        $request->validate([
            'nama_studi' => 'required|string|max:255',
            'deskripsi_studi' => 'nullable|string',
        ]);

        $studi = StudiSekolah::findOrFail($id);
        $studi->update($request->only(['nama_studi', 'deskripsi_studi']));
        return redirect()->back()->with('success', 'Program Studi berhasil diperbarui');
    }

    public function destroyStudi($id)
    {
        StudiSekolah::destroy($id);
        return redirect()->back()->with('success', 'Program Studi dihapus');
    }

    // --- INFO SEKOLAH ---
    public function storeOrUpdateInfoSekolah(Request $request)
    {
        $request->validate([
            'jumlah_guru' => 'required|integer|min:0',
            'jumlah_staff' => 'required|integer|min:0',
            'nama_kepala_sekolah' => 'required|string|max:255',
            'foto_kepala_sekolah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fasilitas' => 'nullable|string', // Validasi baru untuk fasilitas
        ]);

        // Tambahkan 'fasilitas' ke dalam fungsi only()
        $data = $request->only(['jumlah_guru', 'jumlah_staff', 'nama_kepala_sekolah', 'fasilitas']);

        $info = InfoSekolah::first();

        if ($request->hasFile('foto_kepala_sekolah')) {
            if ($info && $info->foto_kepala_sekolah && File::exists(public_path($info->foto_kepala_sekolah))) {
                File::delete(public_path($info->foto_kepala_sekolah));
            }
            
            $folderPath = public_path('assets/kepala_sekolah');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }
            
            $fileName = time() . '_kepala.' . $request->foto_kepala_sekolah->extension();
            $destinationPath = $folderPath . '/' . $fileName;
            
            $this->compressImage($request->file('foto_kepala_sekolah')->getPathname(), $destinationPath);
            
            $data['foto_kepala_sekolah'] = 'assets/kepala_sekolah/' . $fileName;
        }

        if ($info) {
            $info->update($data);
        } else {
            InfoSekolah::create($data);
        }

        return redirect()->back()->with('success', 'Informasi sekolah berhasil disimpan');
    }
}