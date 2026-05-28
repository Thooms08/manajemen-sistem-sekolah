<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_sekolah', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah_guru');
            $table->integer('jumlah_staff');
            $table->string('nama_kepala_sekolah');
            $table->string('foto_kepala_sekolah')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_sekolah');
    }
};