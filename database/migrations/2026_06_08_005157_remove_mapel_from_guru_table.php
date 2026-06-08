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
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn('mapel'); // Menghapus kolom mapel
        });
    }

    public function down()
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->string('mapel')->nullable();
        });
    }
};
