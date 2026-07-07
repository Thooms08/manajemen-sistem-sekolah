<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_sekolah', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Isi UUID untuk data yang sudah ada
        \DB::table('profile_sekolah')->get()->each(function ($row) {
            \DB::table('profile_sekolah')
                ->where('id', $row->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        // Jadikan NOT NULL dan unique setelah diisi
        Schema::table('profile_sekolah', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('profile_sekolah', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
