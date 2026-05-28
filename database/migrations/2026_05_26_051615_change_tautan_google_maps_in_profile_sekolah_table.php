<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('profile_sekolah', function (Blueprint $table) {
        // Mengubah kolom menjadi TEXT agar muat karakter yang panjang
        $table->text('tautan_google_maps')->change();
    });
}

public function down()
{
    Schema::table('profile_sekolah', function (Blueprint $table) {
        $table->string('tautan_google_maps', 255)->change();
    });
}
};
