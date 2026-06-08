<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('status')->default('aktif')->after('alamat');
            $table->string('alasan_nonaktif')->nullable()->after('status');
            $table->string('surat_keterangan')->nullable()->after('alasan_nonaktif'); // path file
            $table->timestamp('tanggal_nonaktif')->nullable()->after('surat_keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['status', 'alasan_nonaktif', 'surat_keterangan', 'tanggal_nonaktif']);
        });
    }
};
