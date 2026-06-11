<?php

namespace App\Http\Controllers\Dokumen;

use Illuminate\Http\Request;
use App\Models\Dokumen\Dokumen;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DokumenController extends Controller
{
    // Halaman Root (Manajemen Dokumen)
    public function index()
    {
        $items = Dokumen::whereNull('parent_id')->orderBy('tipe', 'desc')->orderBy('nama', 'asc')->get();
        return view('admin.dokumen.manajemen_dokumen', compact('items'));
    }

    // Halaman Detail Folder
    public function detailFolder($uuid)
    {
        $folder = Dokumen::where('uuid', $uuid)->firstOrFail();
        if ($folder->tipe !== 'folder') abort(404);

        $items = Dokumen::where('parent_id', $folder->id)->orderBy('tipe', 'desc')->orderBy('nama', 'asc')->get();
        return view('admin.dokumen.detail_folder', compact('folder', 'items'));
    }

    // Buat Folder Baru
    public function storeFolder(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        
        Dokumen::create([
            'nama' => $request->nama,
            'tipe' => 'folder',
            'parent_id' => $request->parent_id ?? null,
        ]);

        return back()->with('success', 'Folder berhasil dibuat!');
    }

    // Upload File (Dipindahkan ke Private)
    public function storeFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();

        // UBAH DISINI: Simpan ke disk 'local' di dalam folder private/dokumen_files
        $path = $file->storeAs('dokumen_files', time() . '_' . $file->getClientOriginalName(), 'local');

        Dokumen::create([
            'nama' => $originalName,
            'tipe' => 'file',
            'ekstensi' => strtolower($extension),
            'file_path' => $path,
            'ukuran' => $size,
            'parent_id' => $request->parent_id ?? null,
        ]);

        return back()->with('success', 'File berhasil diunggah!');
    }

    // Rename Folder / File
    public function rename(Request $request, $uuid)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $item = Dokumen::where('uuid', $uuid)->firstOrFail();
        $item->update(['nama' => $request->nama]);

        return back()->with('success', 'Nama berhasil diubah!');
    }

    // Hapus Folder / File (Dipindahkan ke Private)
    public function destroy($uuid)
    {
        $item = Dokumen::where('uuid', $uuid)->firstOrFail();
        
        // UBAH DISINI: Hapus menggunakan disk 'local'
        if ($item->tipe === 'file' && $item->file_path) {
            Storage::disk('local')->delete($item->file_path);
        } elseif ($item->tipe === 'folder') {
            $this->deleteFolderRecursively($item);
        }

        $item->delete();
        return back()->with('success', 'Item berhasil dihapus!');
    }

    // Fungsi Rekursif Hapus Folder (Dipindahkan ke Private)
    private function deleteFolderRecursively($folder)
    {
        foreach ($folder->children as $child) {
            // UBAH DISINI: Hapus menggunakan disk 'local'
            if ($child->tipe === 'file' && $child->file_path) {
                Storage::disk('local')->delete($child->file_path);
            } else {
                $this->deleteFolderRecursively($child);
            }
            $child->delete();
        }
    }

    // Download File (Dipindahkan ke Private)
    public function download($uuid)
    {
        $item = Dokumen::where('uuid', $uuid)->firstOrFail();
        
        // UBAH DISINI: Ambil dan download dari disk 'local'
        if ($item->tipe === 'file' && Storage::disk('local')->exists($item->file_path)) {
            return Storage::disk('local')->download($item->file_path, $item->nama . '.' . $item->ekstensi);
        }
        return back()->with('error', 'File tidak ditemukan!');
    }

    /**
     * Menampilkan/Stream file secara aman via Signed URL (tanpa perlu session cookie).
     * Route ini di luar middleware auth, diproteksi oleh signature token.
     */
    public function viewFile(Request $request, $uuid)
    {
        // Signed URL sudah divalidasi oleh middleware 'signed' di routes/web.php
        $item = Dokumen::where('uuid', $uuid)->firstOrFail();
        abort_if($item->tipe !== 'file', 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($item->file_path), 404, 'Berkas tidak ditemukan.');

        $path    = $disk->path($item->file_path);
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            'webp' => 'image/webp',
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogg'  => 'video/ogg',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv'  => 'text/csv',
            'txt'  => 'text/plain',
        ];

        $ext      = strtolower($item->ekstensi);
        $mimeType = $mimeMap[$ext] ?? 'application/octet-stream';

        // Semua tipe yang bisa ditampilkan inline di browser → inline
        // doc/docx/xls/xlsx tidak bisa dirender browser → set attachment agar di-download
        $inlineExts = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp',
                       'mp4', 'webm', 'ogg', 'mp3', 'wav', 'txt', 'csv'];
        $disposition = in_array($ext, $inlineExts) ? 'inline' : 'attachment';

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . addslashes($item->nama . '.' . $ext) . '"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('search');
        $output = "";

        // Sesuaikan dengan nama tabel dan kolom Anda
        $dokumens = DB::table('dokumen')
            ->where('judul_dokumen', 'LIKE', '%' . $query . '%')
            ->orWhere('kategori', 'LIKE', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($dokumens->count() > 0) {
            foreach ($dokumens as $dok) {
                // Logika sederhana untuk icon: 
                // Misalnya Anda punya field 'tipe' (folder/file) atau berdasarkan ekstensi file
                $icon = ($dok->kategori == 'Folder') 
                        ? '<i class="bi bi-folder-fill text-warning" style="font-size: 3.5rem;"></i>' 
                        : '<i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 3.5rem;"></i>';

                $output .= '
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 text-center p-3 file-folder-card" style="border-radius: 12px; transition: 0.3s; cursor: pointer;">
                        <div class="icon-wrapper mb-2">
                            ' . $icon . '
                        </div>
                        <h6 class="fw-bold text-dark text-truncate mb-1" title="' . $dok->judul_dokumen . '">
                            ' . $dok->judul_dokumen . '
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">' . date('d M Y', strtotime($dok->created_at)) . '</small>
                    </div>
                </div>';
            }
        } else {
            $output = '
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-muted mb-3 d-block" style="font-size: 3rem; opacity: 0.5;"></i>
                <h6 class="text-muted">Dokumen atau folder tidak ditemukan</h6>
            </div>';
        }

        return response($output);
    }
}