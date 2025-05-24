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
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('day_change_time')->nullable()->after('attendance_type'); // e.g. 00:00
            $table->time('default_start_time')->nullable()->after('day_change_time');
            $table->time('default_end_time')->nullable()->after('default_start_time');
            $table->integer('default_working_time')->nullable()->after('default_end_time'); // in minutes
            $table->boolean('proper_checkin_checkout')->default(false)->after('default_working_time');
            $table->boolean('salary_at_month_end')->default(true)->after('proper_checkin_checkout');
            $table->date('salary_date')->nullable()->after('salary_at_month_end');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'day_change_time',
                'default_start_time',
                'default_end_time',
                'default_working_time',
                'proper_checkin_checkout',
                'salary_at_month_end',
                'salary_date'
            ]);
        });
    }
};
