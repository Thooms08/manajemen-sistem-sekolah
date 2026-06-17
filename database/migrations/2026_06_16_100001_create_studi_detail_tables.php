<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pendukung fitur Detail Program Studi:
 * - studi_kepala   : kepala program studi (guru atau staff, bisa lebih dari 1 prodi)
 * - studi_kelas    : kelas yang masuk program studi (1 kelas hanya 1 prodi)
 * - studi_catatan  : catatan bebas per program studi
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Kepala Program Studi ─────────────────────────────────
        Schema::create('studi_kepala', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_studi')->index();
            $table->enum('tipe', ['guru', 'staff']);
            $table->unsignedBigInteger('id_sumber');
            // Cache nama agar tidak perlu join berat setiap render
            $table->string('nama_kepala', 255);
            $table->string('jabatan', 100)->nullable()->default('Kepala Program Studi');
            $table->timestamps();

            $table->foreign('id_studi')->references('id')->on('studi_sekolah')->onDelete('cascade');
            // Satu guru/staff boleh jadi kepala di banyak prodi — tidak ada unique pada id_sumber
            // Tapi di 1 prodi yang sama, 1 orang tidak boleh muncul duplikat
            $table->unique(['id_studi', 'tipe', 'id_sumber'], 'unique_studi_kepala');
        });

        // ── Kelas yang Masuk Program Studi ───────────────────────
        Schema::create('studi_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_studi')->index();
            // 1 kelas hanya boleh masuk 1 prodi — unique pada id_kelas saja
            $table->unsignedBigInteger('id_kelas')->unique();
            // Cache nama kelas
            $table->string('nama_kelas', 100);
            $table->timestamps();

            $table->foreign('id_studi')->references('id')->on('studi_sekolah')->onDelete('cascade');
        });

        // ── Catatan Program Studi ────────────────────────────────
        Schema::create('studi_catatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_studi')->index();
            $table->string('judul', 255);
            $table->longText('isi');
            $table->timestamps();

            $table->foreign('id_studi')->references('id')->on('studi_sekolah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studi_catatan');
        Schema::dropIfExists('studi_kelas');
        Schema::dropIfExists('studi_kepala');
    }
};
