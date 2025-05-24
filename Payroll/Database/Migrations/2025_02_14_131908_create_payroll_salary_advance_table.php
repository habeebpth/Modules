<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_salary_advance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->text('advance_type'); // AdvanceSalary/HRA
            $table->unsignedBigInteger('employee_id');
            $table->date('request_date');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->string('transaction_reference', 50)->unique()->nullable();
            $table->enum('payment_mode', ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'])->nullable();
            $table->text('repayment_method'); // One-time, Installments
            $table->text('repayment_status'); // Pending, Completed
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_salary_advance');
    }
};
