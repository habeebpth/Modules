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
            $table->unsignedBigInteger('billing_code')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwc_hotel_reservations', function (Blueprint $table) {
            $table->dropColumn('billing_code');
        });
    }
};
