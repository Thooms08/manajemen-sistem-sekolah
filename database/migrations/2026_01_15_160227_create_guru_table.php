<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (pastikan tabel users sudah ada)
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('nama_guru');
            $table->string('email');
            $table->string('no_whatsapp');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};