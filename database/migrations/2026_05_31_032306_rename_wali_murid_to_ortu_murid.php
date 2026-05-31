<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename table wali_murid to ortu_murid
        Schema::rename('wali_murid', 'ortu_murid');

        // Update foreign key in berkas_ppdb table
        Schema::table('berkas_ppdb', function (Blueprint $table) {
            $table->dropForeign(['id_wali']);
            $table->renameColumn('id_wali', 'id_ortu');
            $table->foreign('id_ortu')->references('id')->on('ortu_murid')->onDelete('cascade');
        });

        // Update foreign key in relasi_wali table
        Schema::table('relasi_wali', function (Blueprint $table) {
            $table->dropForeign(['id_wali']);
            $table->renameColumn('id_wali', 'id_ortu');
            $table->foreign('id_ortu')->references('id')->on('ortu_murid')->onDelete('cascade');
        });

        // Update enum value in users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru', 'wali_murid'])->change();
            $table->enum('role', ['admin', 'guru', 'ortu_murid'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse enum value in users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru', 'ortu_murid'])->change();
            $table->enum('role', ['admin', 'guru', 'wali_murid'])->change();
        });

        // Reverse foreign key in relasi_wali table
        Schema::table('relasi_wali', function (Blueprint $table) {
            $table->dropForeign(['id_ortu']);
            $table->renameColumn('id_ortu', 'id_wali');
            $table->foreign('id_wali')->references('id')->on('wali_murid')->onDelete('cascade');
        });

        // Reverse foreign key in berkas_ppdb table
        Schema::table('berkas_ppdb', function (Blueprint $table) {
            $table->dropForeign(['id_ortu']);
            $table->renameColumn('id_ortu', 'id_wali');
            $table->foreign('id_wali')->references('id')->on('wali_murid')->onDelete('cascade');
        });

        // Rename table back
        Schema::rename('ortu_murid', 'wali_murid');
    }
};
