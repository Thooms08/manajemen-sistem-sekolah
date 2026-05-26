<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
Schema::create('keaktifans', function (Blueprint $table) {
    $table->id();
    $table->string('nama_keaktifan');
    $table->string('foto')->nullable();
    $table->unsignedBigInteger('id_kelas');
    $table->date('tanggal'); // Menambahkan kolom tanggal
    $table->text('keterangan')->nullable();
    $table->timestamps();
});

Schema::create('keaktifan_murid', function (Blueprint $table) {
    $table->id();
    $table->foreignId('keaktifan_id')->constrained('keaktifans')->onDelete('cascade');
    $table->foreignId('murid_id')->constrained('murid')->onDelete('cascade');
    $table->boolean('is_active')->default(true); // true = centang, false = X
});
}
};