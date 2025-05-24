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
            $table->integer('confirmation_no')->nullable()->after('no_of_nights');
            $table->date('checkin_date')->nullable()->after('reservation_date');
            $table->date('checkout_date')->nullable()->after('checkin_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwc_hotel_reservations', function (Blueprint $table) {
            $table->dropColumn(['confirmation_no', 'checkin_date','checkout_date']);
        });

    }
};
