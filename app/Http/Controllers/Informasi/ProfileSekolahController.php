<?php

namespace App\Http\Controllers\Informasi;

use App\Http\Traits\RendersUserView;


use App\Models\Informasi\ProfileSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\DataMaster\Guru;
use App\Models\DataMaster\Murid;
use App\Http\Controllers\Controller;

class ProfileSekolahController extends Controller
{
    use RendersUserView;
    public function index()
    {
        $profiles = ProfileSekolah::all();
        return $this->renderView('admin.informasi_sekolah.profile_sekolah', compact('profiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'nis'          => 'required|string|max:50',
            'logo'         => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_sekolah' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi'    => 'required',
            'email'        => 'required|email',
            'no_hp'        => 'nullable',
            'akreditasi'   => 'nullable',
            'tautan_google_maps' => 'nullable',
            'alamat'       => 'nullable',
        ]);

        $data = $request->except(['logo', 'foto_sekolah']);

        // Upload Logo ke storage/app/public/logos/
        if ($request->hasFile('logo')) {
            $logoName = time() . '_logo.' . $request->logo->extension();
            $data['logo'] = $request->logo->storeAs('logos', $logoName, 'public');
        }

        // Upload Foto Sekolah ke storage/app/public/fotos/
        if ($request->hasFile('foto_sekolah')) {
            $fotoName = time() . '_foto.' . $request->foto_sekolah->extension();
            $data['foto_sekolah'] = $request->foto_sekolah->storeAs('fotos', $fotoName, 'public');
        }

        ProfileSekolah::create($data);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $profile = ProfileSekolah::findOrFail($id);

        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'nis'          => 'required|string|max:50',
        ]);

        $data = $request->except(['logo', 'foto_sekolah']);

        if ($request->hasFile('logo')) {
            $this->deleteStorageFile($profile->logo);
            $logoName = time() . '_logo.' . $request->logo->extension();
            $data['logo'] = $request->logo->storeAs('logos', $logoName, 'public');
        }

        if ($request->hasFile('foto_sekolah')) {
            $this->deleteStorageFile($profile->foto_sekolah);
            $fotoName = time() . '_foto.' . $request->foto_sekolah->extension();
            $data['foto_sekolah'] = $request->foto_sekolah->storeAs('fotos', $fotoName, 'public');
        }

        $profile->update($data);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $profile = ProfileSekolah::findOrFail($id);

        $this->deleteStorageFile($profile->logo);
        $this->deleteStorageFile($profile->foto_sekolah);

        $profile->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function deleteImage(Request $request, $id)
    {
        $profile = ProfileSekolah::findOrFail($id);
        $type    = $request->query('type'); // 'logo' atau 'foto_sekolah'

        if (in_array($type, ['logo', 'foto_sekolah'])) {
            $this->deleteStorageFile($profile->$type);
            $profile->$type = null;
            $profile->save();

            return response()->json(['success' => true, 'message' => ucfirst($type) . ' berhasil dihapus']);
        }

        return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
    }

    /**
     * Hapus file dari storage — mendukung path lama (assets/...) maupun path baru.
     */
    private function deleteStorageFile(?string $path): void
    {
        if (!$path) return;

        if (str_starts_with($path, 'assets/')) {
            // File lama masih di public/assets/
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
            return;
        }

        // File baru di storage/app/public/
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
