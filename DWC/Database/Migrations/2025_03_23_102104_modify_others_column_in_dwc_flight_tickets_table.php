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
        Schema::table('dwc_flight_tickets', function (Blueprint $table) {
            $table->string('flight_from', 100)->nullable()->change();
            $table->string('flight_to', 100)->nullable()->change();
            $table->string('flight_no', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('dwc_flight_tickets', function (Blueprint $table) {
        //     $table->dropColumn('flight_from', 100);
        //     $table->dropColumn('flight_to', 100);
        //     $table->dropColumn('flight_no', 50);
        // });
    }
};
