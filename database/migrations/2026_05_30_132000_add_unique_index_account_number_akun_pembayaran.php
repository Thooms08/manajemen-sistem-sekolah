<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('akun_pembayaran', function (Blueprint $table) {
            $table->unique('account_number', 'akun_pembayaran_account_number_unique');
        });
    }

    public function down()
    {
        Schema::table('akun_pembayaran', function (Blueprint $table) {
            $table->dropUnique('akun_pembayaran_account_number_unique');
        });
    }
};
