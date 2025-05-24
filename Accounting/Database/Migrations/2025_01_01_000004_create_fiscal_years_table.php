<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->unique(['company_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_years');
    }
};