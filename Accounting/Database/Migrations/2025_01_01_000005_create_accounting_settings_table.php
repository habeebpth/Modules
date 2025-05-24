<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'setting_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};