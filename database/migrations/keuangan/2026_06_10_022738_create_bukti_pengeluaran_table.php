<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Jalankan di koneksi keuangan_db
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->create('bukti_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengeluaran')->nullable()->index();
            $table->string('bukti_foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->dropIfExists('bukti_pengeluaran');
    }
};
