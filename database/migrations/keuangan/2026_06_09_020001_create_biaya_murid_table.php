<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'keuangan_db';

    public function up(): void
    {
        Schema::connection('keuangan_db')->create('biaya_murid', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('account_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('disabled_reason')->nullable();
            $table->timestamps();

            // Foreign key ke akun_pembayaran dalam database yang sama
            $table->foreign('account_id')
                  ->references('id')
                  ->on('akun_pembayaran')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::connection('keuangan_db')->dropIfExists('biaya_murid');
    }
};
