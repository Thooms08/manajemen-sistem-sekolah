<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus tabel akun_pembayaran dan biaya_murid dari sekolah_db
 * karena kedua tabel ini sudah dipindahkan ke keuangan_db.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop biaya_murid terlebih dahulu karena ada foreign key ke akun_pembayaran
        Schema::table('biaya_murid', function (Blueprint $table) {
            // Drop foreign key sebelum drop tabel
            if (Schema::hasColumn('biaya_murid', 'account_id')) {
                $table->dropForeign(['account_id']);
            }
        });

        Schema::dropIfExists('biaya_murid');
        Schema::dropIfExists('akun_pembayaran');
    }

    public function down(): void
    {
        // Recreate akun_pembayaran
        Schema::create('akun_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number')->nullable()->unique();
            $table->string('account_holder')->nullable();
            $table->boolean('is_qris')->default(false);
            $table->string('qris_image')->nullable();
            $table->timestamps();
        });

        // Recreate biaya_murid
        Schema::create('biaya_murid', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('account_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('disabled_reason')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('akun_pembayaran')->onDelete('set null');
        });
    }
};
