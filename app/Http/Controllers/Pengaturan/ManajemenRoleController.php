<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan\Role;
use App\Models\Pengaturan\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManajemenRoleController extends Controller
{
    private function modules(): array
    {
        return config('modules', []);
    }

    /**
     * Guard: tolak operasi apapun terhadap role admin.
     */
    private function guardAdmin(Role $role, string $aksi = 'mengubah'): bool
    {
        return $role->isAdmin();
    }

    // ── Halaman Utama ────────────────────────────────────────────
    public function index()
    {
        $roles   = Role::withCount('permissions')->orderBy('is_system', 'desc')->orderBy('nama')->get();
        $modules = $this->modules();

        return view('admin.pengaturan.manajemen_role', compact('roles', 'modules'));
    }

    // ── Halaman Edit Permission per Role ─────────────────────────
    public function editPermissions(string $uuid)
    {
        $role = Role::with('permissions')->where('uuid', $uuid)->firstOrFail();

        // Role admin tidak bisa diedit permission-nya
        if ($role->isAdmin()) {
            return redirect()->route('admin.manajemen-role.index')
                ->with('error', 'Hak akses role Administrator tidak dapat diubah demi keamanan sistem.');
        }

        $modules = $this->modules();
        $saved   = $role->permissions->keyBy('modul')->map(fn($p) => $p->aksi ?? []);

        return view('admin.pengaturan.edit_role_permissions', compact('role', 'modules', 'saved'));
    }

    // ── Simpan / Update Permission ────────────────────────────────
    public function savePermissions(Request $request, string $uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        if ($role->isAdmin()) {
            return redirect()->route('admin.manajemen-role.index')
                ->with('error', 'Hak akses role Administrator tidak dapat diubah.');
        }

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['string'],
        ]);

        $modules = $this->modules();

        DB::transaction(function () use ($request, $role, $modules) {
            $role->permissions()->delete();

            foreach ($modules as $modul => $config) {
                $aksi      = (array) $request->input("permissions.{$modul}", []);
                $validAksi = array_values(array_intersect($aksi, $config['aksi']));

                if ($validAksi === []) {
                    continue;
                }

                RolePermission::create([
                    'role_id' => $role->id,
                    'modul'   => $modul,
                    'aksi'    => $validAksi,
                ]);
            }
        });

        return redirect()->route('admin.manajemen-role.index')
            ->with('success', "Hak akses untuk role \"{$role->nama}\" berhasil disimpan.");
    }

    // ── Tambah Role Baru ──────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'warna'     => 'required|in:primary,secondary,success,danger,warning,info,dark',
        ]);

        $slug     = Str::slug($request->nama, '_');
        $baseSlug = $slug;
        $i = 2;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '_' . $i++;
        }

        Role::create([
            'slug'      => $slug,
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'warna'     => $request->warna,
            'is_system' => false,
        ]);

        return back()->with('success', "Role \"{$request->nama}\" berhasil dibuat. Klik Atur Hak Akses untuk mengatur modul yang bisa diakses.");
    }

    // ── Update Nama/Deskripsi Role ────────────────────────────────
    public function update(Request $request, string $uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        if ($role->isAdmin()) {
            return back()->with('error', 'Role Administrator tidak dapat diubah.');
        }

        $request->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'warna'     => 'required|in:primary,secondary,success,danger,warning,info,dark',
        ]);

        $role->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'warna'     => $request->warna,
        ]);

        return back()->with('success', "Role \"{$role->nama}\" berhasil diperbarui.");
    }

    // ── Hapus Role ────────────────────────────────────────────────
    public function destroy(string $uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        if ($role->isAdmin()) {
            return back()->with('error', 'Role Administrator tidak dapat dihapus.');
        }

        if ($role->is_system) {
            return back()->with('error', "Role sistem \"{$role->nama}\" tidak dapat dihapus.");
        }

        $nama = $role->nama;
        $role->delete();

        return back()->with('success', "Role \"{$nama}\" berhasil dihapus.");
    }

    // ── AJAX: Data role untuk modal edit ──────────────────────────
    public function getRole(string $uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();
        return response()->json($role);
    }

    // ── AJAX: Ringkasan permission role ───────────────────────────
    public function getPermissionSummary(string $uuid)
    {
        $role    = Role::with('permissions')->where('uuid', $uuid)->firstOrFail();
        $modules = $this->modules();
        $summary = [];

        foreach ($role->permissions as $perm) {
            $modulConf = $modules[$perm->modul] ?? null;
            if ($modulConf) {
                $summary[] = [
                    'modul' => $modulConf['label'],
                    'icon'  => $modulConf['icon'],
                    'group' => $modulConf['group'],
                    'aksi'  => $perm->aksi ?? [],
                ];
            }
        }

        return response()->json(['role' => $role, 'summary' => $summary]);
    }
}
