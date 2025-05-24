<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('account_id');
            $table->date('reconciliation_date');
            $table->decimal('statement_balance', 15, 2);
            $table->decimal('book_balance', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->enum('status', ['draft', 'completed', 'reviewed'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
            $table->index(['company_id', 'account_id']);
            $table->index(['company_id', 'reconciliation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};