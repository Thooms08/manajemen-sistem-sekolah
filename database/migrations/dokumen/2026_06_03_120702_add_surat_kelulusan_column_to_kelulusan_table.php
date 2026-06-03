<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menggunakan Schema::connection karena tabel ini berada di database 'dokumen_db'
        // Pastikan nama koneksi 'dokumen_db' sudah didaftarkan di config/database.php Anda
        Schema::connection('dokumen_db')->table('kelulusan', function (Blueprint $table) {
            $table->string('surat_kelulusan')->nullable()->after('raport')
                  ->comment('Menyimpan path file privat surat kelulusan resmi murid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('dokumen_db')->table('kelulusan', function (Blueprint $table) {
            $table->dropColumn('surat_kelulusan');
        });
    }
};