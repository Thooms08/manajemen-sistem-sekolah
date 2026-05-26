<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('teaser');
            $table->timestamps();
        });

        Schema::create('foto_artikel', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel artikel
            $table->foreignId('id_artikel')->constrained('artikel')->onDelete('cascade');
            $table->string('foto_artikel'); // Path: resources/asset/artikel
            $table->string('sumber_foto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_artikel');
        Schema::dropIfExists('artikel');
    }
};