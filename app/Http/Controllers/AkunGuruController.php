<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AkunGuruController extends Controller
{
    public function index()
    {
        // Mengambil data guru beserta akun user-nya melalui tabel relasi
        $gurus = Guru::leftJoin('relasi_guru', 'guru.id', '=', 'relasi_guru.id_guru')
            ->leftJoin('users', 'relasi_guru.id_user', '=', 'users.id')
            ->select('guru.id as id_guru', 'guru.nama_guru', 'users.id as id_user', 'users.username')
            ->get();

        return view('dashboard_admin.akun_guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.unique' => 'Username sudah ada.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat User baru
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'rules' => 'guru',
                ]);

                // 2. Simpan ke tabel relasi_guru
                DB::table('relasi_guru')->insert([
                    'id_guru' => $request->id_guru,
                    'id_user' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Akun guru berhasil dibuat!');
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

        return redirect()->back()->with('success', 'Akun guru berhasil diperbarui!');
    }

    public function destroy($id_user)
    {
        try {
            DB::transaction(function () use ($id_user) {
                // Hapus di relasi dulu baru di users
                DB::table('relasi_guru')->where('id_user', $id_user)->delete();
                User::destroy($id_user);
            });

            return redirect()->back()->with('success', 'Akun guru berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus data.']);
        }
    }
    public function search(Request $request)
{
    $query = $request->get('query');
    
    $gurus = Guru::leftJoin('relasi_guru', 'guru.id', '=', 'relasi_guru.id_guru')
        ->leftJoin('users', 'relasi_guru.id_user', '=', 'users.id')
        ->select('guru.id as id_guru', 'guru.nama_guru', 'users.id as id_user', 'users.username')
        ->where('guru.nama_guru', 'LIKE', '%' . $query . '%')
        ->orWhere('users.username', 'LIKE', '%' . $query . '%')
        ->get();

    $output = '';
    if ($gurus->count() > 0) {
        foreach ($gurus as $index => $g) {
            $badge = $g->id_user 
                ? '<span class="badge bg-success-subtle text-success border border-success px-3"><i class="bi bi-person-check me-1"></i> '.$g->username.'</span>'
                : '<span class="text-muted small italic">Belum punya akun</span>';
            
            $tombol = '';
            if (!$g->id_user) {
                $tombol = '<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah'.$g->id_guru.'"><i class="bi bi-person-plus me-1"></i> Buat Akun</button>';
            } else {
                $tombol = '<button class="btn btn-outline-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit'.$g->id_user.'"><i class="bi bi-pencil"></i></button>
                           <form action="'.route('akun-guru.destroy', $g->id_user).'" method="POST" class="d-inline">'.csrf_field().method_field('DELETE').'
                           <button class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Hapus akun login guru ini?\')"><i class="bi bi-trash"></i></button></form>';
            }

            $output .= '<tr>
                <td>'.($index + 1).'</td>
                <td class="fw-bold">'.$g->nama_guru.'</td>
                <td>'.$badge.'</td>
                <td class="text-center">'.$tombol.'</td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="4" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>';
    }

    return response($output);
}
}