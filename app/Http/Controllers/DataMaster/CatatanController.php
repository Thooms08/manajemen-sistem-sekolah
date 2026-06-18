<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;
use App\Models\DataMaster\Catatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanController extends Controller
{
    use RendersUserView;
    /**
     * Admin: tampilkan semua catatan dikelompokkan per user dalam bentuk card.
     * User biasa: tampilkan catatan milik sendiri saja.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $usersWithCatatan = User::whereHas('catatans')
                ->withCount('catatans')
                ->with(['catatans' => function ($q) {
                    $q->latest()->take(1);
                }])
                ->get();

            return $this->renderView('admin.data_master.catatan', compact('usersWithCatatan'));
        }

        // User non-admin: tampilkan catatan milik sendiri dengan view khusus user
        $catatans = Catatan::where('id_user', $user->id)->latest()->get();
        return view('user.catatan', compact('catatans'));
    }

    /**
     * Admin: lihat semua catatan dari satu user tertentu.
     */
    public function showByUser($id_user)
    {
        $pemilik  = User::findOrFail($id_user);
        $catatans = Catatan::where('id_user', $id_user)->latest()->get();

        return $this->renderView('admin.data_master.catatan_detail_user', compact('pemilik', 'catatans'));
    }

    /**
     * Store catatan baru (admin atau user menyimpan catatan sendiri).
     */
    public function store(Request $request)
    {
        $request->validate([
            'label'   => 'required|string|max:100',
            'catatan' => 'required|string|max:5000',
        ]);

        Catatan::create([
            'id_user' => Auth::id(),
            'label'   => $request->label,
            'catatan' => $request->catatan,
        ]);

        // Admin kembali ke halaman admin catatan, user kembali ke halaman user catatan
        if (Auth::user()->role === 'admin') {
            return redirect()->route('catatan.index')->with('success', 'Catatan berhasil disimpan!');
        }

        return redirect()->route('user.catatan.index')->with('success', 'Catatan berhasil disimpan!');
    }

    /**
     * AJAX: ambil data catatan untuk modal edit.
     */
    public function getEditData(string $uuid)
    {
        $catatan = Catatan::where('uuid', $uuid)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        return response()->json($catatan);
    }

    /**
     * Update catatan.
     */
    public function update(Request $request, string $uuid)
    {
        $request->validate([
            'label'   => 'required|string|max:100',
            'catatan' => 'required|string|max:5000',
        ]);

        $catatan = Catatan::where('uuid', $uuid)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $catatan->update([
            'label'   => $request->label,
            'catatan' => $request->catatan,
        ]);

        if (Auth::user()->role === 'admin') {
            return redirect()->route('catatan.index')->with('success', 'Catatan berhasil diperbarui!');
        }

        return redirect()->route('user.catatan.index')->with('success', 'Catatan berhasil diperbarui!');
    }

    /**
     * Hapus catatan.
     * Admin bisa hapus catatan siapapun; user biasa hanya milik sendiri.
     */
    public function destroy(string $uuid)
    {
        $user  = Auth::user();
        $query = Catatan::where('uuid', $uuid);

        if ($user->role !== 'admin') {
            $query->where('id_user', $user->id);
        }

        $catatan = $query->firstOrFail();
        $catatan->delete();

        if ($user->role === 'admin') {
            return redirect()->route('catatan.index')->with('success', 'Catatan berhasil dihapus!');
        }

        return redirect()->route('user.catatan.index')->with('success', 'Catatan berhasil dihapus!');
    }
}
