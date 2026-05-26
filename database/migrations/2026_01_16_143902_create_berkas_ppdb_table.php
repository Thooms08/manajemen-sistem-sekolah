<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berkas_ppdb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_murid')->constrained('murid')->onDelete('cascade');
            $table->foreignId('id_wali')->constrained('wali_murid')->onDelete('cascade');
            $table->string('foto_ijazah_sd');
            $table->string('foto_kk');
            $table->string('ktp_ayah');
            $table->string('ktp_ibu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_ppdb');
    }
};
