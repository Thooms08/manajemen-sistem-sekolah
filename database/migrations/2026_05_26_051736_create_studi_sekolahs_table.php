<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
        {
            Schema::create('studi_sekolah', function (Blueprint $table) {
                $table->id();
                $table->string('nama_studi');
                $table->string('deskripsi_studi')->nullable(); // Kolom deskripsi singkat
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studi_sekolahs');
    }
};
