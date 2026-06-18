<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya admin yang merupakan role sistem bawaan — tidak bisa dihapus.
        // Role lain (guru, staff, dll) dibuat secara dinamis oleh admin.
        $roles = [
            ['slug' => 'admin', 'nama' => 'Administrator', 'deskripsi' => 'Akses penuh ke seluruh sistem', 'warna' => 'danger', 'is_system' => true],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        // Hapus role sistem yang salah ditetapkan sebelumnya (guru, ortu)
        // agar hanya admin yang tersisa sebagai is_system
        Role::whereIn('slug', ['guru', 'ortu', 'wali_murid'])
            ->where('is_system', true)
            ->update(['is_system' => false]);
    }
}
