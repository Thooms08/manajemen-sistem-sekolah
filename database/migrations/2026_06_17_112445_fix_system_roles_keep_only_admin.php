<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hanya role 'admin' yang berstatus is_system = true.
     * Role lain (guru, ortu, wali_murid, dll) tidak boleh is_system
     * agar admin dapat mengelolanya secara dinamis.
     */
    public function up(): void
    {
        // 1. Pastikan admin tetap is_system
        DB::table('roles')
            ->where('slug', 'admin')
            ->update(['is_system' => true]);

        // 2. Semua role selain admin: is_system = false
        DB::table('roles')
            ->where('slug', '!=', 'admin')
            ->where('is_system', true)
            ->update(['is_system' => false]);

        // 3. Hapus role 'guru' dan 'ortu' yang dibuat seeder lama
        //    HANYA jika tidak ada user yang masih memakai slug tersebut
        //    (agar tidak merusak data user yang sudah ada)
        foreach (['guru', 'ortu', 'wali_murid'] as $slug) {
            $adaUser = DB::table('users')->where('role', $slug)->exists();
            if (!$adaUser) {
                DB::table('roles')->where('slug', $slug)->delete();
            }
        }
    }

    public function down(): void
    {
        // Kembalikan guru & ortu sebagai is_system jika di-rollback
        DB::table('roles')
            ->whereIn('slug', ['guru', 'ortu', 'wali_murid'])
            ->update(['is_system' => true]);
    }
};
