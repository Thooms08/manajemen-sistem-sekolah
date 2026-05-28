<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profile_sekolah')->insert([
            'nama_sekolah'       => 'SMK Informatika Indonesia',
            'nis'                => '1020304050',
            'logo'               => 'assets/logos/logo.png', 
            'foto_sekolah'       => 'assets/fotos/sekolah.jpg', 
            'deskripsi'          => 'Menjadi lembaga pendidikan kejuruan yang unggul, menghasilkan lulusan yang kompeten di bidang teknologi informasi, berkarakter mulia, dan siap bersaing di era digital global.',
            'alamat'             => 'Jl. Tekno Raya No. 40, Kota Informatika, Indonesia',
            'tautan_google_maps' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2737678534825!2d106.816666!3d-6.222222!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTMnMTkuOCJTIDEwNsKwNDknMDAuMCJF!5e0!3m2!1sid!2sid!4v1620000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'no_hp'              => '081234567890',
            'email'              => 'info@smkinformatika.sch.id',
            'akreditasi'         => 'A',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }
}