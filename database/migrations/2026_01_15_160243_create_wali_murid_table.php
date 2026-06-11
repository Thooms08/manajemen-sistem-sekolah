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
            $table->string('nama_wali');
            $table->string('tempat_lahir_wali')->nullable();
            $table->date('tgl_lahir_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();
            $table->enum('status_wali', ['hidup', 'meninggal'])->nullable();
            $table->string('hubungan_wali')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_murid');
    }
};
