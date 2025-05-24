<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');

            $table->index(['company_id', 'account_id']);
            $table->index(['journal_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
