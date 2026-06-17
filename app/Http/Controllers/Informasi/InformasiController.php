<?php

namespace App\Http\Controllers\Informasi;

use Illuminate\Http\Request;
use App\Models\Informasi\Dokumentasi;
use App\Models\Informasi\ProgramSekolah;
use App\Models\Informasi\Prestasi;
use App\Models\Informasi\Artikel;
use App\Models\Informasi\StudiSekolah;
use App\Models\Informasi\InfoSekolah;
use App\Models\Informasi\Brosur;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class InformasiController extends Controller
{
    /**
     * Kompresi gambar ke < 100KB menggunakan PHP GD, lalu simpan ke Storage disk 'public'.
     * Mengembalikan path relatif yang tersimpan (contoh: kegiatan/1234_kegiatan.jpg)
     */
    private function compressAndStore($file, string $folder): string
    {
        $sourcePath = $file->getPathname();
        $fileName   = time() . '_' . $folder . '.' . $file->extension();
        $tmpPath    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $info = getimagesize($sourcePath);
        $mime = $info ? $info['mime'] : null;
        $maxSize = 102400; // 100 KB

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $image   = imagecreatefromjpeg($sourcePath);
            $quality = 90;
            imagejpeg($image, $tmpPath, $quality);
            while (file_exists($tmpPath) && filesize($tmpPath) > $maxSize && $quality > 10) {
                $quality -= 10;
                imagejpeg($image, $tmpPath, $quality);
            }
            imagedestroy($image);
        } elseif ($mime === 'image/png') {
            $image = imagecreatefrompng($sourcePath);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $tmpPath, 9);
            imagedestroy($image);
        } else {
            copy($sourcePath, $tmpPath);
        }

        $storagePath = $folder . '/' . $fileName;
        Storage::disk('public')->put($storagePath, file_get_contents($tmpPath));
        @unlink($tmpPath);

        return $storagePath;
    }

    /**
     * Hapus file lama dari storage (mendukung path lama 'assets/...' maupun path baru).
     */
    private function deleteOldFile(?string $path): void
    {
        if (!$path) return;

        // Path lama masih di public/assets/...
        if (str_starts_with($path, 'assets/')) {
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
            return;
        }

        // Path baru di storage/app/public/...
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function index()
    {
        $kegiatan    = Dokumentasi::all();
        $programs    = ProgramSekolah::all();
        $prestasi    = Prestasi::all();
        $artikels    = Artikel::all();
        $studiList   = StudiSekolah::all();
        $infoSekolah = InfoSekolah::first();
        $brosurList  = Brosur::latest()->get();

        return view('admin.informasi_sekolah.informasi', compact(
            'kegiatan', 'programs', 'prestasi', 'artikels', 'studiList', 'infoSekolah', 'brosurList'
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
            $data['foto_kegiatan'] = $this->compressAndStore($request->file('foto_kegiatan'), 'kegiatan');
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
            $this->deleteOldFile($data->foto_kegiatan);
            $updateData['foto_kegiatan'] = $this->compressAndStore($request->file('foto_kegiatan'), 'kegiatan');
        }
        $data->update($updateData);
        return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroyKegiatan($id)
    {
        $data = Dokumentasi::findOrFail($id);
        $this->deleteOldFile($data->foto_kegiatan);
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
            $data['foto_prestasi'] = $this->compressAndStore($request->file('foto_prestasi'), 'prestasi');
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
            $this->deleteOldFile($prestasi->foto_prestasi);
            $updateData['foto_prestasi'] = $this->compressAndStore($request->file('foto_prestasi'), 'prestasi');
        }

        $prestasi->update($updateData);
        return redirect()->back()->with('success', 'Data prestasi berhasil diperbarui');
    }

    public function destroyPrestasi($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        if ($prestasi->foto_prestasi && $prestasi->foto_prestasi !== '-') {
            $this->deleteOldFile($prestasi->foto_prestasi);
        }
        $prestasi->delete();
        return redirect()->back()->with('success', 'Prestasi berhasil dihapus');
    }

    public function showArtikel($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        $sekolah = \App\Models\Informasi\ProfileSekolah::first() ?? \App\Models\Informasi\InfoSekolah::first() ?? InfoSekolah::first(); 
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
            $fotoPath = $this->compressAndStore($request->file('foto_artikel'), 'artikel');
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
            $this->deleteOldFile($artikel->foto_artikel);
            $updateData['foto_artikel'] = $this->compressAndStore($request->file('foto_artikel'), 'artikel');
        }

        $artikel->update($updateData);
        return redirect()->back()->with('success', 'Artikel diperbarui');
    }

    public function destroyArtikel($id)
    {
        $artikel = Artikel::findOrFail($id);
        $this->deleteOldFile($artikel->foto_artikel);
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

    // --- BROSUR ---
    public function storeBrosur(Request $request)
    {
        $request->validate([
            'label'     => 'required|string|max:150',
            'file_brosur' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $path = $request->file('file_brosur')->store('brosur', 'public');

        Brosur::create([
            'label'     => $request->label,
            'path_file' => $path,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Brosur berhasil disimpan');
    }

    public function updateBrosur(Request $request, $id)
    {
        $request->validate([
            'label'       => 'required|string|max:150',
            'file_brosur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'deskripsi'   => 'nullable|string|max:500',
        ]);

        $brosur = Brosur::findOrFail($id);

        $updateData = [
            'label'     => $request->label,
            'deskripsi' => $request->deskripsi,
        ];

        if ($request->hasFile('file_brosur')) {
            // Hapus file lama
            if ($brosur->path_file && Storage::disk('public')->exists($brosur->path_file)) {
                Storage::disk('public')->delete($brosur->path_file);
            }
            $updateData['path_file'] = $request->file('file_brosur')->store('brosur', 'public');
        }

        $brosur->update($updateData);
        return redirect()->back()->with('success', 'Brosur berhasil diperbarui');
    }

    public function destroyBrosur($id)
    {
        $brosur = Brosur::findOrFail($id);
        if ($brosur->path_file && Storage::disk('public')->exists($brosur->path_file)) {
            Storage::disk('public')->delete($brosur->path_file);
        }
        $brosur->delete();
        return redirect()->back()->with('success', 'Brosur berhasil dihapus');
    }

    // --- INFO SEKOLAH ---
    public function storeOrUpdateInfoSekolah(Request $request)
    {
        $request->validate([
            'jumlah_guru'         => 'nullable|integer|min:0',
            'jumlah_staff'        => 'nullable|integer|min:0',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'foto_kepala_sekolah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fasilitas'           => 'nullable|string',
        ]);

        $data = $request->only(['jumlah_guru', 'jumlah_staff', 'nama_kepala_sekolah', 'fasilitas']);

        $info = InfoSekolah::first();

        if ($request->hasFile('foto_kepala_sekolah')) {
            $this->deleteOldFile($info ? $info->foto_kepala_sekolah : null);
            $data['foto_kepala_sekolah'] = $this->compressAndStore($request->file('foto_kepala_sekolah'), 'kepala_sekolah');
        }

        if ($info) {
            $info->update($data);
        } else {
            InfoSekolah::create($data);
        }

        return redirect()->back()->with('success', 'Informasi sekolah berhasil disimpan');
    }
}