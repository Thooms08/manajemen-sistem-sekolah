<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrtuMurid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AkunOrtuController extends Controller
{
    public function index()
    {
        // Mengambil data ortu murid, murid terkait, dan akun user melalui tabel relasi
        $ortus = DB::table('ortu_murid')
            ->join('murid', 'ortu_murid.id_murid', '=', 'murid.id')
            ->leftJoin('relasi_wali', 'ortu_murid.id', '=', 'relasi_wali.id_ortu')
            ->leftJoin('users', 'relasi_wali.id_user', '=', 'users.id')
            ->select(
                'ortu_murid.id as id_ortu',
                'ortu_murid.nama_ayah',
                'ortu_murid.nama_ibu',
                'murid.nama_lengkap',
                'murid.nisn',
                'users.id as id_user',
                'users.username'
            )
            ->get();

        return view('admin.akun_ortu', compact('ortus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_ortu' => 'required|exists:ortu_murid,id',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.unique' => 'Username sudah ada.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan ke tabel users
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role' => 'ortu_murid',
                ]);

                // 2. Simpan ke tabel relasi_wali
                DB::table('relasi_wali')->insert([
                    'id_ortu' => $request->id_ortu,
                    'id_user' => $user->id
                ]);
            });

            return redirect()->back()->with('success', 'Akun ortu murid berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        $request->validate([
            'username' => 'required|unique:users,username,' . $id_user,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->back()->with('success', 'Akun ortu murid berhasil diperbarui!');
    }

    public function destroy($id_user)
    {
        try {
            DB::transaction(function () use ($id_user) {
                DB::table('relasi_wali')->where('id_user', $id_user)->delete();
                User::destroy($id_user);
            });
            return redirect()->back()->with('success', 'Akun ortu murid berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus akun.']);
        }
    }

    // Tambahkan method ini di dalam AkunOrtuController
public function search(Request $request)
{
    $output = "";
    $query = $request->get('query');

    $ortus = DB::table('ortu_murid')
        ->join('murid', 'ortu_murid.id_murid', '=', 'murid.id')
        ->leftJoin('relasi_wali', 'ortu_murid.id', '=', 'relasi_wali.id_ortu')
        ->leftJoin('users', 'relasi_wali.id_user', '=', 'users.id')
        ->select(
            'ortu_murid.id as id_ortu',
            'ortu_murid.nama_ayah',
            'ortu_murid.nama_ibu',
            'murid.nama_lengkap',
            'murid.nisn',
            'users.id as id_user',
            'users.username'
        )
        ->where(function($q) use ($query) {
            $q->where('murid.nama_lengkap', 'LIKE', '%' . $query . '%')
              ->orWhere('murid.nisn', 'LIKE', '%' . $query . '%')
              ->orWhere('ortu_murid.nama_ayah', 'LIKE', '%' . $query . '%')
              ->orWhere('ortu_murid.nama_ibu', 'LIKE', '%' . $query . '%')
              ->orWhere('users.username', 'LIKE', '%' . $query . '%');
        })
        ->get();

    if ($ortus->count() > 0) {
        foreach ($ortus as $o) {
            $badgeAkun = $o->id_user 
                ? '<span class="badge bg-success-subtle text-success border border-success px-3"><i class="bi bi-shield-check me-1"></i> '.$o->username.'</span>'
                : '<span class="text-muted small italic">Belum ada akun</span>';

            $tombolAksi = "";
            if (!$o->id_user) {
                $tombolAksi = '<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah'.$o->id_ortu.'">
                                <i class="bi bi-plus-circle me-1"></i> Buat Akun
                               </button>';
            } else {
                $tombolAksi = '<button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit'.$o->id_user.'">
                                <i class="bi bi-pencil"></i>
                               </button>
                               <form action="'.route('akun-ortu.destroy', $o->id_user).'" method="POST" class="d-inline">
                                '.csrf_field().' '.method_field('DELETE').'
                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Hapus akun login ortu ini?\')">
                                    <i class="bi bi-trash"></i>
                                </button>
                               </form>';
            }

            $output .= '
            <tr>
                <td>
                    <div class="fw-bold">'.$o->nama_lengkap.'</div>
                    <small class="text-muted">NISN: '.$o->nisn.'</small>
                </td>
                <td>
                    <small class="d-block">Ayah: '.$o->nama_ayah.'</small>
                    <small class="d-block">Ibu: '.$o->nama_ibu.'</small>
                </td>
                <td>'.$badgeAkun.'</td>
                <td class="text-center">'.$tombolAksi.'</td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="4" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>';
    }

    return response($output);
}
}