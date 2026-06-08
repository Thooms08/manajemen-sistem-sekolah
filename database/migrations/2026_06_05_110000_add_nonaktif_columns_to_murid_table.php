<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            // Kolom untuk data nonaktif murid
            $table->string('alasan_nonaktif')->nullable()->after('status');
            $table->string('surat_pernyataan')->nullable()->after('alasan_nonaktif'); // path file
            $table->timestamp('tanggal_nonaktif')->nullable()->after('surat_pernyataan');
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropColumn(['alasan_nonaktif', 'surat_pernyataan', 'tanggal_nonaktif']);
        });
    }
};
