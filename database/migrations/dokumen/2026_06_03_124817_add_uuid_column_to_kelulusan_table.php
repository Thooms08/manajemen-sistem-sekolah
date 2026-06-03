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
        Schema::connection('dokumen_db')->table('kelulusan', function (Blueprint $table) {
            // Menambahkan ->unique() agar nilai kolom harus unik
            $table->string('uuid')->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('dokumen_db')->table('kelulusan', function (Blueprint $table) {
            // Menghapus index unik dan kolom uuid jika migration di-rollback
            $table->dropUnique(['uuid']); 
            $table->dropColumn('uuid');
        });
    }
};