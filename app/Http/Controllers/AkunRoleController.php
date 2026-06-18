<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengaturan\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AkunRoleController extends Controller
{
    /**
     * Tampilkan halaman manajemen akun role.
     * Berisi: semua role (kecuali admin) + daftar akun yang sudah dibuat.
     */
    public function index()
    {
        // Ambil semua role dinamis (kecuali role admin bawaan sistem)
        $roles = Role::where('slug', '!=', 'admin')
            ->orderBy('nama')
            ->get();

        // Akun yang sudah dibuat admin (role bukan admin, guru, wali_murid, ortu_murid)
        // — kita ambil semua user yang role-nya bukan role sistem lama
        $sistemLama = ['admin', 'guru', 'wali_murid', 'ortu_murid'];

        $akuns = User::whereNotIn('role', $sistemLama)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                // Tambahkan data role-nya dari tabel roles
                $user->role_data = Role::where('slug', $user->role)->first();
                return $user;
            });

        return view('admin.akun_role', compact('roles', 'akuns'));
    }

    /**
     * Simpan akun baru untuk role tertentu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_slug'             => 'required|exists:roles,slug',
            'username'              => 'required|string|max:100|unique:users,username',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'role_slug.required'        => 'Pilih role terlebih dahulu.',
            'role_slug.exists'          => 'Role tidak valid.',
            'username.required'         => 'Username wajib diisi.',
            'username.unique'           => 'Username sudah digunakan.',
            'password.required'         => 'Password wajib diisi.',
            'password.min'              => 'Password minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        // Pastikan role yang dipilih bukan admin
        $role = Role::where('slug', $request->role_slug)->firstOrFail();
        if ($role->isAdmin()) {
            return back()->with('error', 'Tidak dapat membuat akun dengan role Administrator.');
        }

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role_slug,
        ]);

        return back()->with('success', "Akun \"<strong>{$request->username}</strong>\" dengan role \"<strong>{$role->nama}</strong>\" berhasil dibuat.");
    }

    /**
     * Update username dan/atau password akun.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Jangan izinkan edit akun admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun Administrator tidak dapat diubah.');
        }

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_slug' => 'required|exists:roles,slug',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.min'      => 'Password minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $role = Role::where('slug', $request->role_slug)->firstOrFail();
        if ($role->isAdmin()) {
            return back()->with('error', 'Tidak dapat menetapkan role Administrator pada akun ini.');
        }

        $user->username = $request->username;
        $user->role     = $request->role_slug;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', "Akun \"<strong>{$user->username}</strong>\" berhasil diperbarui.");
    }

    /**
     * Hapus akun user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Akun Administrator tidak dapat dihapus.');
        }

        $username = $user->username;
        $user->delete();

        return back()->with('success', "Akun \"<strong>{$username}</strong>\" berhasil dihapus.");
    }

    /**
     * AJAX: cek ketersediaan username (spasi & duplikat).
     */
    public function checkUsername(Request $request)
    {
        $username  = $request->get('username', '');
        $excludeId = $request->get('exclude_id'); // untuk mode edit

        // Cek spasi
        if (str_contains($username, ' ')) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'Username tidak boleh mengandung spasi.',
            ]);
        }

        // Kosong — tidak perlu cek DB
        if (trim($username) === '') {
            return response()->json(['status' => 'empty']);
        }

        // Cek duplikat di DB
        $query = User::where('username', $username);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            return response()->json([
                'status'  => 'taken',
                'message' => "Username \"{$username}\" sudah digunakan. Silakan pilih yang lain.",
            ]);
        }

        return response()->json([
            'status'  => 'available',
            'message' => "Username tersedia.",
        ]);
    }

    /**
     * AJAX: cari akun berdasarkan username atau nama role.
     */
    public function search(Request $request)
    {
        $keyword   = $request->get('search', '');
        $sistemLama = ['admin', 'guru', 'wali_murid', 'ortu_murid'];

        $akuns = User::whereNotIn('role', $sistemLama)
            ->where(function ($q) use ($keyword) {
                $q->where('username', 'LIKE', "%{$keyword}%")
                  ->orWhere('role', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $user->role_data = Role::where('slug', $user->role)->first();
                return $user;
            });

        // Ambil juga semua role untuk keperluan dropdown di modal edit
        $roles = Role::where('slug', '!=', 'admin')->orderBy('nama')->get();

        $output = '';
        if ($akuns->count() > 0) {
            foreach ($akuns as $index => $u) {
                $roleData  = $u->role_data;
                $warna     = $roleData ? $roleData->warna : 'secondary';
                $roleName  = $roleData ? $roleData->nama  : $u->role;

                $editBtn = '<button class="btn btn-sm btn-outline-success border-0"
                    title="Edit"
                    onclick=\'openEditModal('
                    . $u->id . ', "'
                    . addslashes($u->username) . '", "'
                    . addslashes($u->role) . '")\'>
                    <i class="bi bi-pencil-square"></i>
                </button>';

                $deleteBtn = '<form action="' . route('akun-role.destroy', $u->id) . '" method="POST" class="d-inline">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus"
                        onclick="return confirm(\'Hapus akun ' . addslashes($u->username) . '?\')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>';

                $output .= '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td class="fw-bold">' . e($u->username) . '</td>
                    <td>
                        <span class="badge bg-' . $warna . ' px-3 py-2 rounded-pill">' . e($roleName) . '</span>
                    </td>
                    <td class="text-muted small">' . $u->created_at->isoFormat('D MMM YYYY, HH:mm') . '</td>
                    <td class="text-center">
                        <div class="btn-group">' . $editBtn . $deleteBtn . '</div>
                    </td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="5" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-40"></i>
                Tidak ada akun ditemukan.
            </td></tr>';
        }

        return response($output);
    }
}
