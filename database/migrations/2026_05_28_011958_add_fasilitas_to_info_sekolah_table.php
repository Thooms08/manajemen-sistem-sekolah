<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('info_sekolah', function (Blueprint $table) {
            $table->text('fasilitas')->nullable()->after('foto_kepala_sekolah');
        });
    }

    public function down()
    {
        Schema::table('info_sekolah', function (Blueprint $table) {
            $table->dropColumn('fasilitas');
        });
    }
};