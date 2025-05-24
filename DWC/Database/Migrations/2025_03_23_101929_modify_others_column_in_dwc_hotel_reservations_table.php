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
        Schema::table('dwc_hotel_reservations', function (Blueprint $table) {
            $table->string('hotel_id', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwc_hotel_reservations', function (Blueprint $table) {
            // $table->dropColumn('hotel_id', 100);
        });
    }
};
