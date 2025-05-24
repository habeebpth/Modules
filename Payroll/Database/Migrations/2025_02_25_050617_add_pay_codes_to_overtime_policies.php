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
        Schema::table('overtime_policies', function (Blueprint $table) {
            $table->string('pay_code_working_days')->nullable();
            $table->string('pay_code_week_end')->nullable();
            $table->string('pay_code_holiday')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_policies', function (Blueprint $table) {
            $table->dropColumn(['pay_code_working_days', 'pay_code_week_end', 'pay_code_holiday']);

        });
    }
};
