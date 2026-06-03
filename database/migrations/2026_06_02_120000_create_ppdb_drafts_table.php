<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // Unique session identifier
            $table->string('ip_address')->nullable();
            
            // Data Murid
            $table->string('nama_lengkap')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->char('nisn', 10)->nullable();
            $table->char('nik', 16)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->text('alamat_detail')->nullable();
            $table->string('transportasi')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('alamat_email')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->integer('anak_ke')->nullable();
            $table->integer('jlm_saudara')->nullable();
            $table->integer('jumlah_kakak')->nullable();
            $table->integer('jumlah_adik')->nullable();
            
            // Data Orang Tua
            $table->string('nama_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tgl_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('status_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tgl_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('status_ibu')->nullable();
            
            // Data Wali
            $table->string('nama_wali')->nullable();
            $table->string('hubungan_wali')->nullable();
            $table->string('tempat_lahir_wali')->nullable();
            $table->date('tgl_lahir_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('status_wali')->nullable();
            
            // Dokumen paths (stored as JSON)
            $table->json('dokumen_paths')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_drafts');
    }
};
