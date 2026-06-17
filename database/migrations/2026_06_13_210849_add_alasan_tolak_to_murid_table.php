<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            // Alasan penolakan pendaftaran oleh admin
            $table->text('alasan_tolak')->nullable()->after('alasan_nonaktif');
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropColumn('alasan_tolak');
        });
    }
};
