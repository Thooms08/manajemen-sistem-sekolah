<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom id_mapel dan id_kelas ke tabel jadwal_mengajar.
     * Kedua kolom ini merujuk ke tabel pengajar (kombinasi guru-mapel-kelas)
     * sehingga jadwal hanya bisa dibuat untuk mapel & kelas yang sudah
     * terdaftar pada data guru bersangkutan.
     */
    public function up(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            // Tambah setelah id_guru
            $table->foreignId('id_mapel')
                  ->nullable()
                  ->after('id_guru')
                  ->constrained('mapel')
                  ->onDelete('cascade');

            $table->foreignId('id_kelas')
                  ->nullable()
                  ->after('id_mapel')
                  ->constrained('kelas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->dropForeign(['id_mapel']);
            $table->dropForeign(['id_kelas']);
            $table->dropColumn(['id_mapel', 'id_kelas']);
        });
    }
};
