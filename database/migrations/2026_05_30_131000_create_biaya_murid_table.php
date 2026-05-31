<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('biaya_murid', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('account_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('akun_pembayaran')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('biaya_murid');
    }
};
