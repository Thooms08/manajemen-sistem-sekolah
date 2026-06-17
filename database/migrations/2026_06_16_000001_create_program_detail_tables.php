<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration gabungan untuk semua tabel pendukung fitur Detail Program Sekolah:
 * - program_pembina    : pembina program (guru atau staff)
 * - program_anggota   : murid yang mengikuti program
 * - program_bagan     : bagan organisasi program (ketua, sekretaris, dll)
 * - program_catatan   : catatan bebas per program (program kerja, jadwal, dll)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel Pembina Program ────────────────────────────────
        Schema::create('program_pembina', function (Blueprint $table) {
            $table->id();
            // FK ke tabel program_sekolah (DB utama)
            $table->unsignedBigInteger('id_program')->index();
            // Tipe sumber: 'guru' atau 'staff'
            $table->enum('tipe', ['guru', 'staff']);
            // ID dari tabel guru atau staff (sesuai tipe)
            $table->unsignedBigInteger('id_sumber');
            // Peran pembina, misal: "Pembina Utama", "Asisten Pembina"
            $table->string('peran', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_program')->references('id')->on('program_sekolah')->onDelete('cascade');
        });

        // ── Tabel Anggota / Murid Program ───────────────────────
        Schema::create('program_anggota', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_program')->index();
            // FK ke murid (cross-table, tanpa FK constraint agar fleksibel)
            $table->unsignedBigInteger('id_murid')->index();
            $table->timestamps();

            $table->unique(['id_program', 'id_murid'], 'unique_program_anggota');
            $table->foreign('id_program')->references('id')->on('program_sekolah')->onDelete('cascade');
        });

        // ── Tabel Bagan Organisasi Program ──────────────────────
        Schema::create('program_bagan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_program')->index();
            // Jabatan dinamis: Ketua, Wakil Ketua, Sekretaris, Bendahara, Anggota, dll
            $table->string('jabatan', 100);
            // Siapa yang memegang jabatan ini — bisa murid, bisa guru/staff
            $table->enum('tipe_pemegang', ['murid', 'guru', 'staff']);
            $table->unsignedBigInteger('id_pemegang');
            // Nama cache supaya tidak perlu join berat
            $table->string('nama_pemegang', 255);
            // Urutan tampilan di bagan
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('id_program')->references('id')->on('program_sekolah')->onDelete('cascade');
        });

        // ── Tabel Catatan Program ────────────────────────────────
        Schema::create('program_catatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_program')->index();
            $table->string('judul', 255);
            // Konten rich-text / plain text dari TipTap / textarea
            $table->longText('isi');
            $table->timestamps();

            $table->foreign('id_program')->references('id')->on('program_sekolah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_catatan');
        Schema::dropIfExists('program_bagan');
        Schema::dropIfExists('program_anggota');
        Schema::dropIfExists('program_pembina');
    }
};
