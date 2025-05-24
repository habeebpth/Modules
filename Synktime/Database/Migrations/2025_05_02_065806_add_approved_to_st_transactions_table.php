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
        Schema::table('st_transactions', function (Blueprint $table) {
            $table->enum('approved', ['y', 'n'])->default('y')->after('attendance_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('st_transactions', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
};
