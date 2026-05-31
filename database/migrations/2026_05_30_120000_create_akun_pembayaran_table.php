<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('akun_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->boolean('is_qris')->default(false);
            $table->string('qris_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('akun_pembayaran');
    }
};
