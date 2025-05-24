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
        Schema::table('synking_history', function (Blueprint $table) {
            $table->string('sync_type')->nullable()->after('to_date')->comment('attendance, department, area, employee');
            $table->integer('total_synced')->default(0)->after('sync_type');

            // Make these columns nullable since they won't be needed for all sync types
            $table->bigInteger('project_id')->nullable()->change();
            $table->bigInteger('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('synking_history', function (Blueprint $table) {
            $table->dropColumn(['sync_type', 'total_synced']);

            // Revert the nullable changes if needed
            $table->bigInteger('project_id')->nullable(false)->change();
            $table->bigInteger('employee_id')->nullable(false)->change();
        });
    }
};
