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
        // Check if the column doesn't already exist
        if (!Schema::hasColumn('event_queue_statuses', 'countdown_minutes')) {
            Schema::table('event_queue_statuses', function (Blueprint $table) {
                $table->integer('countdown_minutes')->default(20)->after('ladies');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_queue_statuses', function (Blueprint $table) {
            $table->dropColumn('countdown_minutes');
        });
    }
};