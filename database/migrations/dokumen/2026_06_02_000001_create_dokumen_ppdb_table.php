<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Jalankan di koneksi dokumen_db
    protected $connection = 'dokumen_db';

    public function up(): void
    {
        Schema::connection('dokumen_db')->create('dokumen_ppdb', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_murid')->nullable()->index();

            // Foto & Dokumen — semua nullable
            $table->string('pasfoto')->nullable();
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
        });
    }

    public function down(): void
    {
        Schema::connection('dokumen_db')->dropIfExists('dokumen_ppdb');
    }
};
