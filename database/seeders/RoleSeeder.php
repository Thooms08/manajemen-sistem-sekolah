<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'admin', 'nama' => 'Administrator',  'deskripsi' => 'Akses penuh ke seluruh sistem', 'warna' => 'danger',   'is_system' => true],
            ['slug' => 'guru',  'nama' => 'Guru',           'deskripsi' => 'Tenaga pengajar sekolah',       'warna' => 'primary',  'is_system' => true],
            ['slug' => 'ortu',  'nama' => 'Orang Tua',      'deskripsi' => 'Orang tua / wali murid',        'warna' => 'success',  'is_system' => true],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }
    }
}
