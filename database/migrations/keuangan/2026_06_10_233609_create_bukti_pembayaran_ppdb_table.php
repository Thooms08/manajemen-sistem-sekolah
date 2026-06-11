<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->create('bukti_pembayaran_ppdb', function (Blueprint $table) {
            $table->id();
            // id_murid dari DB utama (cross-DB, tidak bisa FK constraint)
            $table->unsignedBigInteger('id_murid')->index();
            // Nama biaya yang dibayarkan (denormalisasi agar mudah dibaca)
            $table->string('nama_biaya')->nullable();
            // Path file relatif dari disk 'local' (storage/app/private/...)
            $table->string('file_path');
            // Nama asli file untuk keperluan display
            $table->string('file_name');
            // Ukuran file dalam bytes
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->dropIfExists('bukti_pembayaran_ppdb');
    }
};
