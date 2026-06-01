<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_form_settings', function (Blueprint $table) {
            $table->id();
            $table->string('field_name')->unique();
            $table->string('field_label');
            $table->string('field_category'); // murid, ortu, wali, dokumen, biaya
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_form_settings');
    }
};
