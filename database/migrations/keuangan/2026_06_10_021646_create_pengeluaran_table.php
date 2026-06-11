<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pengeluaran');          // operasional|gaji_staff|gaji_guru|lainnya
            $table->string('keterangan_lainnya')->nullable(); // hanya diisi jika jenis = lainnya
            $table->string('nama_pengeluaran');
            $table->text('deskripsi')->nullable();
            $table->bigInteger('nominal');
            $table->integer('qty');
            $table->bigInteger('total');
            $table->enum('status', ['tersedia', 'dihapus'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->dropIfExists('pengeluaran');
    }
};
