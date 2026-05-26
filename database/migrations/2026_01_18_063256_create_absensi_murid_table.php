<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('absensi_murid', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_murid')->constrained('murid')->onDelete('cascade');
        $table->foreignId('id_guru')->constrained('users'); // Guru yang mengabsen
        $table->date('tanggal');
        $table->enum('status', ['hadir', 'tidak_hadir', 'libur']);
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_murid');
    }
};
