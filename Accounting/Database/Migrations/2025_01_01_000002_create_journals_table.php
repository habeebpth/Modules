<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('journal_number', 50)->unique();
            $table->date('date');
            $table->text('description');
            $table->string('reference_type')->nullable(); // invoice, payment, expense, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('total_debit', 15, 2);
            $table->decimal('total_credit', 15, 2);
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('reversed_by')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};