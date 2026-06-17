<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_guru')->constrained('guru')->onDelete('cascade');
            $table->foreignId('id_mapel')->constrained('mapel')->onDelete('cascade');
            $table->unsignedBigInteger('id_kelas')->index();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 100)->nullable();
            $table->timestamps();

            // Cegah duplikasi jadwal: 1 guru, 1 hari, 1 jam_mulai hanya boleh 1 kelas
            $table->unique(['id_guru', 'hari', 'jam_mulai'], 'unique_jadwal_guru');
            // Cegah benturan kelas: 1 kelas, 1 hari, 1 jam_mulai hanya boleh 1 guru
            $table->unique(['id_kelas', 'hari', 'jam_mulai'], 'unique_jadwal_kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajar');
    }
};
