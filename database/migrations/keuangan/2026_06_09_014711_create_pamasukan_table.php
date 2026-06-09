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
        Schema::connection('keuangan_db')->create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_murid')->nullable()->index();
            $table->string('jenis_pemasukan')->nullable();
            $table->string('jenis_biaya_ppdb')->nullable();
            $table->string('keterangan_lainnya')->nullable();
            $table->string('keterangan_biaya')->nullable();
            $table->bigInteger('nominal')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('total')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->dropIfExists('pemasukan');
    }
};
