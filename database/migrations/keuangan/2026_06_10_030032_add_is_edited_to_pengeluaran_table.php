<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->table('pengeluaran', function (Blueprint $table) {
            // Apakah data ini pernah diedit? Jika ya, simpan timestamp edit terakhir
            $table->timestamp('edited_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });
    }
};
