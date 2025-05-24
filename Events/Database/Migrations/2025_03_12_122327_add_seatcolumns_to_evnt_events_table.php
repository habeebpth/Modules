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
        Schema::table('evnt_events', function (Blueprint $table) {
            $table->unsignedInteger('no_of_seats_for_guests')->nullable()->after('registration_fees_amount');
            $table->unsignedInteger('guest_seat_start')->nullable()->after('no_of_seats_for_guests');
            $table->unsignedInteger('guest_seat_end')->nullable()->after('guest_seat_start');
            $table->unsignedInteger('no_of_seats_for_participants')->nullable()->after('guest_seat_end');
            $table->unsignedInteger('participants_seat_start')->nullable()->after('no_of_seats_for_participants');
            $table->unsignedInteger('participants_seat_end')->nullable()->after('participants_seat_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evnt_events', function (Blueprint $table) {
            $table->dropColumn([
                'no_of_seats_for_guests',
                'guest_seat_start',
                'guest_seat_end',
                'no_of_seats_for_participants'
            ]);

        });
    }
};
