<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('account_id');
            $table->string('period_type', 20); // monthly, quarterly, yearly
            $table->integer('period_number'); // 1-12 for monthly, 1-4 for quarterly, 1 for yearly
            $table->decimal('budgeted_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            
            $table->unique(['company_id', 'fiscal_year_id', 'account_id', 'period_type', 'period_number'], 'budget_unique');
            $table->index(['company_id', 'fiscal_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};