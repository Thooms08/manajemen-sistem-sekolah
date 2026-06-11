<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ortu_murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_murid')->constrained('murid')->onDelete('cascade');
            // Data Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tgl_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable();
            $table->enum('status_ayah', ['hidup', 'meninggal'])->nullable();
            // Data Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tgl_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();
            $table->enum('status_ibu', ['hidup', 'meninggal'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortu_murid');
    }
};
