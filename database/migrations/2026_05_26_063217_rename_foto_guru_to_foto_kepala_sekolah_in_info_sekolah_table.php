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
        Schema::table('info_sekolah', function (Blueprint $table) {
            // Check if column exists before renaming
            if (Schema::hasColumn('info_sekolah', 'foto_guru')) {
                // Mengubah nama kolom 'foto_guru' menjadi 'foto_kepala_sekolah'
                $table->renameColumn('foto_guru', 'foto_kepala_sekolah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_sekolah', function (Blueprint $table) {
            // Check if column exists before renaming
            if (Schema::hasColumn('info_sekolah', 'foto_kepala_sekolah')) {
                // Mengembalikan nama kolom dari 'foto_kepala_sekolah' menjadi 'foto_guru' jika dilakukan rollback
                $table->renameColumn('foto_kepala_sekolah', 'foto_guru');
            }
        });
    }
};  