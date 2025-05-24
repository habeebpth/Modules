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
        if (!Schema::hasColumn('salary_slips', 'expense_id')) {
            Schema::table('salary_slips', function (Blueprint $table) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('salary_slips', 'expense_id')) {
            Schema::table('salary_slips', function (Blueprint $table) {
                $table->dropColumn('expense_id');
            });
        }
    }
};
