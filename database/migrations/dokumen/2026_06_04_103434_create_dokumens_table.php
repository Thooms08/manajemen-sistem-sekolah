<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Mendefinisikan koneksi database kedua
        Schema::connection('dokumen_db')->create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('nama');
            $table->enum('tipe', ['folder', 'file']);
            $table->string('ekstensi')->nullable(); // pdf, docx, png, dll
            $table->string('file_path')->nullable();
            $table->integer('ukuran')->nullable(); // dalam bytes
            $table->foreignId('parent_id')->nullable()->constrained('dokumens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('dokumen_db')->dropIfExists('dokumens');
    }
};