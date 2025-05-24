<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('account_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('module_name'); // hotel, inventory, payroll, etc.
            $table->string('mapping_type'); // revenue, expense, receivable, etc.
            $table->unsignedBigInteger('account_id');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
            $table->unique(['company_id', 'module_name', 'mapping_type']);
            $table->index(['company_id', 'module_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_mappings');
    }
};