<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::table('keaktifans', function (Blueprint $table) {
        $table->foreignId('id_guru')->nullable()->constrained('guru');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keaktifans', function (Blueprint $table) {
            //
        });
    }
};
