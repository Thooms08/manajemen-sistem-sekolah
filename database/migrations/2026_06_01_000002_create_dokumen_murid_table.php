<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create table using default connection
        // Note: To use separate dokumen_db, add DOKUMEN_DB_CONNECTION=sqlite to .env
        Schema::create('dokumen_murid', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_murid')->nullable();
            
            // Dokumen - All nullable
            $table->string('ktp_ayah')->nullable();
            $table->string('ktp_ibu')->nullable();
            $table->string('ktp_wali')->nullable();
            $table->string('kartu_keluarga')->nullable();
            $table->string('akte_kelahiran')->nullable();
            $table->string('ijazah_terakhir')->nullable();
            $table->string('transkip_nilai')->nullable();
            $table->string('surat_kelulusan')->nullable();
            $table->string('surat_keterangan_hasil_ujian')->nullable();
            $table->string('surat_pindahan')->nullable();
            $table->string('formulir_fisik')->nullable();
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index('id_murid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_murid');
    }
};
