<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('closing_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('journal_id');
            $table->enum('type', ['revenue', 'expense', 'dividend', 'summary']);
            $table->date('closing_date');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->timestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
            
            $table->index(['company_id', 'fiscal_year_id']);
            $table->index(['company_id', 'closing_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_entries');
    }
};