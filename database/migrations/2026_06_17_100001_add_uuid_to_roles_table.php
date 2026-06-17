<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Isi UUID untuk data yang sudah ada
        \DB::table('roles')->get()->each(function ($role) {
            \DB::table('roles')->where('id', $role->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // Setelah diisi, baru set unique & not null
        Schema::table('roles', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
