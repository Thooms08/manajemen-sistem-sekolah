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
        Schema::table('users', function (Blueprint $table) {
            // Mengubah nama kolom 'rules' menjadi 'role'
            $table->renameColumn('rules', 'role');
            
            // Opsional: Jika tipe datanya belum ENUM ('admin', 'guru'), 
            // Anda bisa mengubah tipenya juga dengan menghapus komentar di bawah ini:
            // $table->enum('role', ['admin', 'guru'])->default('guru')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengembalikan nama kolom dari 'role' menjadi 'rules' jika dilakukan rollback
            $table->renameColumn('role', 'rules');
        });
    }
};