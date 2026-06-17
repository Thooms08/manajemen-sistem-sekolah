<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pendukung fitur Detail Prestasi:
 * - prestasi_detail  : kolom tambahan per prestasi (jenis, tanggal, tingkat, dll)
 * - prestasi_murid   : murid yang meraih prestasi tersebut
 * - prestasi_catatan : catatan bebas per prestasi
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Detail Utama Prestasi ────────────────────────────────
        Schema::create('prestasi_detail', function (Blueprint $table) {
            $table->id();
            // FK ke tabel prestasi yang sudah ada
            $table->unsignedBigInteger('id_prestasi')->unique();
            // Jenis: 'murid' atau 'sekolah'
            $table->enum('jenis', ['murid', 'sekolah'])->default('murid');
            // Bidang lomba/kompetisi
            $table->string('bidang', 150)->nullable();
            // Tingkat: Sekolah, Kecamatan, Kabupaten/Kota, Provinsi, Nasional, Internasional
            $table->string('tingkat', 100)->nullable();
            // Peringkat / juara
            $table->string('peringkat', 100)->nullable();
            // Nama penyelenggara
            $table->string('penyelenggara', 255)->nullable();
            // Tanggal pelaksanaan
            $table->date('tanggal_pelaksanaan')->nullable();
            // Lokasi
            $table->string('lokasi', 255)->nullable();
            // Untuk jenis sekolah: nama tim/unit yang mewakili
            $table->string('nama_tim', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_prestasi')->references('id')->on('prestasi')->onDelete('cascade');
        });

        // ── Murid Peraih Prestasi ────────────────────────────────
        Schema::create('prestasi_murid', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prestasi')->index();
            $table->unsignedBigInteger('id_murid')->index();
            // Peran murid dalam prestasi ini (Ketua Tim, Anggota, Peserta, dll)
            $table->string('peran', 100)->nullable()->default('Peserta');
            $table->timestamps();

            $table->unique(['id_prestasi', 'id_murid'], 'unique_prestasi_murid');
            $table->foreign('id_prestasi')->references('id')->on('prestasi')->onDelete('cascade');
        });

        // ── Catatan Prestasi ─────────────────────────────────────
        Schema::create('prestasi_catatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prestasi')->index();
            $table->string('judul', 255);
            $table->longText('isi');
            $table->timestamps();

            $table->foreign('id_prestasi')->references('id')->on('prestasi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_catatan');
        Schema::dropIfExists('prestasi_murid');
        Schema::dropIfExists('prestasi_detail');
    }
};
