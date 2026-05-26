<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('foto_prestasi', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel prestasi. Jika prestasi dihapus, foto otomatis terhapus (cascade)
            $table->foreignId('id_prestasi')->constrained('prestasi')->onDelete('cascade');
            $table->string('foto'); // Menyimpan path file gambar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_prestasi');
    }
};