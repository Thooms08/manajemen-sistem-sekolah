<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            // nis_lama: diisi saat PPDB/create, tidak harus unik (murid pindahan bisa pakai NIS sekolah asal)
            $table->string('nis_lama', 20)->nullable()->after('nik');

            // nis_baru: diisi saat admin edit/konfirmasi, harus unik (NIS resmi dari sekolah ini)
            $table->string('nis_baru', 20)->nullable()->unique()->after('nis_lama');
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropUnique(['nis_baru']);
            $table->dropColumn(['nis_lama', 'nis_baru']);
        });
    }
};
