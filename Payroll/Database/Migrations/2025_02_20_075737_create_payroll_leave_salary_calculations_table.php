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
        Schema::create('payroll_leave_salary_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_type')->nullable();
            $table->string('employee_grade')->nullable();
            $table->string('month', 7); // Format: YYYY-MM
            $table->integer('year');
            $table->integer('no_of_days_in_month')->nullable();
            $table->integer('no_of_months_in_year')->nullable();

            // Leave counts
            $table->integer('sl_full_pay')->default(0);
            $table->integer('sl_half_pay')->default(0);
            $table->integer('taken_leave')->default(0); // Renamed from "paid_leave"
            $table->integer('absent')->default(0);
            $table->integer('combo_offs')->default(0);
            $table->integer('total_leave_earned')->default(0);

            // Leave balance
            $table->integer('opening_leave_balance')->default(0);
            $table->integer('closing_leave_balance')->default(0);
            $table->integer('opening_excess_leave')->default(0);
            $table->integer('closing_excess_leave')->default(0);
            $table->integer('excess_leave_taken')->default(0);

            // Salary details
            $table->decimal('salary_basic', 10, 2)->default(0);
            $table->decimal('salary_spay', 10, 2)->default(0);
            $table->decimal('salary_hra', 10, 2)->default(0);
            $table->decimal('salary_incentive', 10, 2)->default(0);
            $table->decimal('salary_gross', 10, 2)->default(0);
            $table->decimal('salary_net', 10, 2)->default(0);
            $table->decimal('salary_leave', 10, 2)->default(0);
            $table->decimal('salary_advance', 10, 2)->default(0);
            $table->decimal('salary_hra_advance', 10, 2)->default(0);
            $table->decimal('salary_ot', 10, 2)->default(0);
            $table->decimal('total_deduction', 10, 2)->default(0);

            // Overtime details
            $table->decimal('ot1_hrs', 5, 2)->default(0);
            $table->decimal('ot1_rate', 10, 2)->default(0);
            $table->decimal('ot1_amt', 10, 2)->default(0);
            $table->decimal('ot2_hrs', 5, 2)->default(0);
            $table->decimal('ot2_rate', 10, 2)->default(0);
            $table->decimal('ot2_amt', 10, 2)->default(0);
            $table->decimal('ot_total_hrs', 5, 2)->default(0);
            $table->decimal('ot_total_amt', 10, 2)->default(0);

            // Other details
            $table->integer('days_worked')->default(0);
            $table->text('remarks')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_leave_salary_calculations');
    }
};
