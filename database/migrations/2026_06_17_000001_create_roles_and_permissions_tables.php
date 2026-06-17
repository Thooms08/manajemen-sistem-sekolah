<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel Role Dinamis ───────────────────────────────────
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            // Slug unik, dipakai sebagai value di kolom users.role
            $table->string('slug', 50)->unique();
            $table->string('nama', 100);
            $table->string('deskripsi', 255)->nullable();
            // Warna badge di UI: success, primary, warning, danger, info, secondary
            $table->string('warna', 30)->default('secondary');
            // Role bawaan sistem (admin, guru, ortu) tidak bisa dihapus
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // ── Tabel Hak Akses per Modul ────────────────────────────
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            // Kode modul (sesuai config/modules.php yang akan dibuat)
            $table->string('modul', 100);
            // Aksi: view, create, edit, delete (bisa beberapa, disimpan sebagai JSON array)
            $table->json('aksi')->nullable();
            $table->timestamps();

            $table->unique(['role_id', 'modul'], 'unique_role_modul');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
    }
};
