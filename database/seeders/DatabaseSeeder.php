<?php

namespace Database\Seeders;

use App\Models\User;
// Import class Seeder dari framework
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data Admin
        User::create([
            'username' => 'admin_sekolah',
            'password' => 'password123', // Otomatis dihash oleh Model User
            'rules'    => 'admin',
        ]);
        
        // Data Guru
        User::create([
            'username' => 'guru_budi',
            'password' => 'password123',
            'rules'    => 'guru',
        ]);

        // Data Wali Murid
        User::create([
            'username' => 'ortu_siswa',
            'password' => 'password123',
            'rules'    => 'wali_murid',
        ]);
    }
}