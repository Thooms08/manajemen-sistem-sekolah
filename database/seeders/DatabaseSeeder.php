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
            'role'    => 'admin',
        ]);
        
        // Data Guru
        User::create([
            'username' => 'guru_budi',
            'password' => 'password123',
            'role'    => 'guru',
        ]);

        $this->call([
            ProfileSekolahSeeder::class,
        ]);
    }
}