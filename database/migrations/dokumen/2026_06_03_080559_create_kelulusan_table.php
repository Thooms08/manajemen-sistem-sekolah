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
        Schema::connection('dokumen_db')->create('kelulusan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_murid')->nullable();
            $table->string('status')->nullable(); // 'lulus' atau 'tidak lulus'
            $table->year('tahun_lulus')->nullable();
            $table->string('ijazah')->nullable(); // Path file ijazah
            $table->string('raport')->nullable(); // Path file raport
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('dokumen_db')->dropIfExists('kelulusan');
    }
};