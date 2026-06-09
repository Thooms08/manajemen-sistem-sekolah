<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->table('pemasukan', function (Blueprint $table) {
            // 'tersedia' = aktif normal, 'dihapus' = soft-delete logis
            $table->enum('status', ['tersedia', 'dihapus'])
                  ->default('tersedia')
                  ->after('total');
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->table('pemasukan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
