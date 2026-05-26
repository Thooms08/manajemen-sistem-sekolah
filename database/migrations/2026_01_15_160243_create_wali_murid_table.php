<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali_murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_murid')->constrained('murid')->onDelete('cascade');
            // Data Ayah
            $table->string('nama_ayah');
            $table->string('tempat_lahir_ayah');
            $table->date('tgl_lahir_ayah');
            $table->string('pendidikan_ayah');
            $table->string('pekerjaan_ayah');
            $table->decimal('penghasilan_ayah', 15, 2);
            $table->enum('status_ayah', ['hidup', 'meninggal']);
            // Data Ibu
            $table->string('nama_ibu');
            $table->string('tempat_lahir_ibu');
            $table->date('tgl_lahir_ibu');
            $table->string('pendidikan_ibu');
            $table->string('pekerjaan_ibu');
            $table->decimal('penghasilan_ibu', 15, 2);
            $table->enum('status_ibu', ['hidup', 'meninggal']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_murid');
    }
};