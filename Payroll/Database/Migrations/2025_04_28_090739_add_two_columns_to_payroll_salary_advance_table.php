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
        Schema::table('payroll_salary_advance', function (Blueprint $table) {
            $table->integer('number_of_installments')->nullable()->after('repayment_status');
            $table->boolean('deduct_from_salary')->default(false)->after('number_of_installments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_salary_advance', function (Blueprint $table) {
            $table->dropColumn('number_of_installments');
            $table->dropColumn('deduct_from_salary');
        });
    }
};
