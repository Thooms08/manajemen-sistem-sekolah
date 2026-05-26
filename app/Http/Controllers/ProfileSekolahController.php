<?php

namespace App\Http\Controllers;

use App\Models\ProfileSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Guru;
use App\Models\Murid;

class ProfileSekolahController extends Controller
{
    public function index()
    {
        $profiles = ProfileSekolah::all();
        return view('dashboard_admin.profile_sekolah', compact('profiles'));
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

    $data = $request->all();

    // Pastikan folder exist
    if (!File::exists(public_path('assets/logos'))) {
        File::makeDirectory(public_path('assets/logos'), 0755, true);
    }
    if (!File::exists(public_path('assets/fotos'))) {
        File::makeDirectory(public_path('assets/fotos'), 0755, true);
    }

    // Upload Logo
    if ($request->hasFile('logo')) {
        $logoName = time() . '_logo.' . $request->logo->extension();
        $request->logo->move(public_path('assets/logos'), $logoName);
        $data['logo'] = 'assets/logos/' . $logoName;
    }

    // Upload Foto Sekolah
    if ($request->hasFile('foto_sekolah')) {
        $fotoName = time() . '_foto.' . $request->foto_sekolah->extension();
        $request->foto_sekolah->move(public_path('assets/fotos'), $fotoName);
        $data['foto_sekolah'] = 'assets/fotos/' . $fotoName;
    }

    ProfileSekolah::create($data);

    return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
}
    public function update(Request $request, $id)
    {
        $profile = ProfileSekolah::findOrFail($id);
        
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'nis' => 'required|string|max:50',
        ]);

        $data = $request->all();

        if ($request->hasFile('logo')) {
            if (File::exists(public_path($profile->logo))) {
                File::delete(public_path($profile->logo));
            }
            $logoName = time() . '_logo.' . $request->logo->extension();
            $request->logo->move(public_path('assets/logos'), $logoName);
            $data['logo'] = 'assets/logos/' . $logoName;
        }

        if ($request->hasFile('foto_sekolah')) {
            if (File::exists(public_path($profile->foto_sekolah))) {
                File::delete(public_path($profile->foto_sekolah));
            }
            $fotoName = time() . '_foto.' . $request->foto_sekolah->extension();
            $request->foto_sekolah->move(public_path('assets/fotos'), $fotoName);
            $data['foto_sekolah'] = 'assets/fotos/' . $fotoName;
        }

        $profile->update($data);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $profile = ProfileSekolah::findOrFail($id);
        
        if (File::exists(public_path($profile->logo))) File::delete(public_path($profile->logo));
        if (File::exists(public_path($profile->foto_sekolah))) File::delete(public_path($profile->foto_sekolah));
        
        $profile->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    // Tambahkan method ini di dalam class ProfileSekolahController
public function deleteImage(Request $request, $id)
{
    $profile = ProfileSekolah::findOrFail($id);
    $type = $request->query('type'); // 'logo' atau 'foto_sekolah'

    if (in_array($type, ['logo', 'foto_sekolah'])) {
        if ($profile->$type && File::exists(public_path($profile->$type))) {
            File::delete(public_path($profile->$type));
        }
        
        $profile->$type = null; // Set kolom jadi null di DB
        $profile->save();

        return response()->json(['success' => true, 'message' => ucfirst($type) . ' berhasil dihapus']);
    }

    return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
}
}