<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('code', 20);
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('type', ['sales', 'purchase', 'both'])->default('both');
            $table->decimal('rate', 5, 2); // Tax rate percentage
            $table->unsignedBigInteger('tax_account_id')->nullable(); // Account for tax collection
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tax_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_codes');
    }
};