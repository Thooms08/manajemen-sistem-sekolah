<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah kolom 'role' dari ENUM(admin, guru, wali_murid) menjadi VARCHAR(50).
     * Ini diperlukan agar role dinamis yang dibuat admin (misal: staff_a, operator, dsb)
     * bisa disimpan tanpa error "Data truncated for column 'role'".
     */
    public function up(): void
    {
        // Gunakan raw SQL karena Doctrine DBAL kadang tidak mendukung perubahan ENUM
        // dengan cara yang konsisten di semua versi Laravel/MySQL.
        DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(50) NOT NULL DEFAULT 'admin'");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (hanya nilai lama yang diketahui)
        // Data yang sudah menggunakan role dinamis akan hilang jika di-rollback
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','guru','wali_murid') NOT NULL DEFAULT 'admin'");
    }
};
