<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('info_ppdb', function (Blueprint $table) {
            $table->id();
            $table->string('foto_poster'); // Path: resources/asset/poster
            $table->string('info_ppdb');
            $table->date('ppdb_awal');
            $table->date('ppdb_akhir');
            $table->timestamps();
        });

        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('foto_prestasi'); // Path: resources/asset/prestasi
            $table->string('judul_prestasi');
            $table->string('deskripsi_prestasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
        Schema::dropIfExists('info_ppdb');
    }
};