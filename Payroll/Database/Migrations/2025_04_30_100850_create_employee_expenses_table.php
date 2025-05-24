<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('expense_type');
            $table->unsignedBigInteger('employee_id');
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->text('details')->nullable();
            $table->string('approval_status')->default('pending'); // pending/approved/rejected
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->dateTime('approval_date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('payment_mode')->nullable(); // cash, bank, etc.
            $table->string('repayment_method')->nullable(); // monthly, one-time, etc.
            $table->integer('no_instalments')->nullable();
            $table->boolean('salary_recovery')->default(false);
            $table->string('repayment_status')->nullable(); // pending/cleared/partial
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_expenses');
    }
};
