<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->char('nisn', 10);
            $table->char('nik', 16);
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->string('rt_rw');
            $table->string('desa_kelurahan');
            $table->string('kota_kabupaten');
            $table->string('provinsi');
            $table->text('alamat_detail');
            $table->string('transportasi');
            $table->string('no_hp');
            $table->string('alamat_email');
            $table->string('sekolah_asal');
            $table->decimal('tinggi_badan', 5, 2); // Contoh: 170.50
            $table->decimal('berat_badan', 5, 2);  // Contoh: 65.20
            $table->integer('anak_ke');
            $table->integer('jlm_saudara');
            $table->integer('jumlah_kakak');
            $table->integer('jumlah_adik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};