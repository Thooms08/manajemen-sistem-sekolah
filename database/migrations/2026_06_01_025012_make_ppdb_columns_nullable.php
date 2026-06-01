<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make murid table columns nullable
        Schema::table('murid', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->change();
            $table->string('jenis_kelamin')->nullable()->change();
            $table->string('nisn', 10)->nullable()->change();
            $table->string('nik')->nullable()->change();
            $table->string('tempat_lahir')->nullable()->change();
            $table->date('tgl_lahir')->nullable()->change();
            $table->string('rt_rw')->nullable()->change();
            $table->string('desa_kelurahan')->nullable()->change();
            $table->string('kota_kabupaten')->nullable()->change();
            $table->string('provinsi')->nullable()->change();
            $table->text('alamat_detail')->nullable()->change();
            $table->string('transportasi')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->string('alamat_email')->nullable()->change();
            $table->string('sekolah_asal')->nullable()->change();
            $table->decimal('tinggi_badan', 5, 2)->nullable()->change();
            $table->decimal('berat_badan', 5, 2)->nullable()->change();
            $table->integer('anak_ke')->nullable()->change();
            $table->integer('jlm_saudara')->nullable()->change();
            $table->integer('jumlah_kakak')->nullable()->change();
            $table->integer('jumlah_adik')->nullable()->change();
        });

        // Make ortu_murid table columns nullable
        Schema::table('ortu_murid', function (Blueprint $table) {
            $table->string('nama_ayah')->nullable()->change();
            $table->string('tempat_lahir_ayah')->nullable()->change();
            $table->date('tgl_lahir_ayah')->nullable()->change();
            $table->string('pendidikan_ayah')->nullable()->change();
            $table->string('pekerjaan_ayah')->nullable()->change();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable()->change();
            $table->string('status_ayah')->nullable()->change();
            $table->string('nama_ibu')->nullable()->change();
            $table->string('tempat_lahir_ibu')->nullable()->change();
            $table->date('tgl_lahir_ibu')->nullable()->change();
            $table->string('pendidikan_ibu')->nullable()->change();
            $table->string('pekerjaan_ibu')->nullable()->change();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable()->change();
            $table->string('status_ibu')->nullable()->change();
        });

        // Make wali_murid table columns nullable (already nullable, but ensure)
        Schema::table('wali_murid', function (Blueprint $table) {
            $table->string('nama_wali')->nullable()->change();
            $table->string('tempat_lahir_wali')->nullable()->change();
            $table->date('tgl_lahir_wali')->nullable()->change();
            $table->string('pendidikan_wali')->nullable()->change();
            $table->string('pekerjaan_wali')->nullable()->change();
            $table->decimal('penghasilan_wali', 15, 2)->nullable()->change();
            $table->string('status_wali')->nullable()->change();
            $table->string('hubungan_wali')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert murid table columns
        Schema::table('murid', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable(false)->change();
            $table->string('jenis_kelamin')->nullable(false)->change();
            $table->string('nisn', 10)->nullable(false)->change();
            $table->string('nik')->nullable(false)->change();
            $table->string('tempat_lahir')->nullable(false)->change();
            $table->date('tgl_lahir')->nullable(false)->change();
            $table->string('rt_rw')->nullable(false)->change();
            $table->string('desa_kelurahan')->nullable(false)->change();
            $table->string('kota_kabupaten')->nullable(false)->change();
            $table->string('provinsi')->nullable(false)->change();
            $table->text('alamat_detail')->nullable(false)->change();
            $table->string('transportasi')->nullable(false)->change();
            $table->string('no_hp')->nullable(false)->change();
            $table->string('alamat_email')->nullable(false)->change();
            $table->string('sekolah_asal')->nullable(false)->change();
            $table->decimal('tinggi_badan', 5, 2)->nullable(false)->change();
            $table->decimal('berat_badan', 5, 2)->nullable(false)->change();
            $table->integer('anak_ke')->nullable(false)->change();
            $table->integer('jlm_saudara')->nullable(false)->change();
            $table->integer('jumlah_kakak')->nullable(false)->change();
            $table->integer('jumlah_adik')->nullable(false)->change();
        });

        // Revert ortu_murid table columns
        Schema::table('ortu_murid', function (Blueprint $table) {
            $table->string('nama_ayah')->nullable(false)->change();
            $table->string('tempat_lahir_ayah')->nullable(false)->change();
            $table->date('tgl_lahir_ayah')->nullable(false)->change();
            $table->string('pendidikan_ayah')->nullable(false)->change();
            $table->string('pekerjaan_ayah')->nullable(false)->change();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable(false)->change();
            $table->string('status_ayah')->nullable(false)->change();
            $table->string('nama_ibu')->nullable(false)->change();
            $table->string('tempat_lahir_ibu')->nullable(false)->change();
            $table->date('tgl_lahir_ibu')->nullable(false)->change();
            $table->string('pendidikan_ibu')->nullable(false)->change();
            $table->string('pekerjaan_ibu')->nullable(false)->change();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable(false)->change();
            $table->string('status_ibu')->nullable(false)->change();
        });

        // Revert wali_murid table columns
        Schema::table('wali_murid', function (Blueprint $table) {
            $table->string('nama_wali')->nullable(false)->change();
            $table->string('tempat_lahir_wali')->nullable(false)->change();
            $table->date('tgl_lahir_wali')->nullable(false)->change();
            $table->string('pendidikan_wali')->nullable(false)->change();
            $table->string('pekerjaan_wali')->nullable(false)->change();
            $table->decimal('penghasilan_wali', 15, 2)->nullable(false)->change();
            $table->string('status_wali')->nullable(false)->change();
            $table->string('hubungan_wali')->nullable(false)->change();
        });
    }
};
