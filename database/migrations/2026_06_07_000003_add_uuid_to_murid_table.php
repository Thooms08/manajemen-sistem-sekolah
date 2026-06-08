<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // Isi uuid untuk data yang sudah ada
        \DB::table('murid')->whereNull('uuid')->get()->each(function ($row) {
            \DB::table('murid')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // Jadikan NOT NULL setelah data diisi semua
        Schema::table('murid', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
