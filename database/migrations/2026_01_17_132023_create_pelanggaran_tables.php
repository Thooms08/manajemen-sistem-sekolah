<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('aturan_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggaran');
            $table->integer('skor');
            $table->timestamps();
        });

        Schema::create('pelanggaran_murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_murid')->constrained('murid')->onDelete('cascade');
            $table->foreignId('id_aturan_pelanggaran')->constrained('aturan_pelanggaran')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelanggaran_murid');
        Schema::dropIfExists('aturan_pelanggaran');
    }
};