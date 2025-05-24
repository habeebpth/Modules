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
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('registration_code', 20)->nullable()->after('mobile');
            $table->unsignedInteger('allotted_seats_start')->nullable()->after('registration_code');
            $table->unsignedInteger('allotted_seats_end')->nullable()->after('allotted_seats_start');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'registration_code',
                'allotted_seats_start',
                'allotted_seats_end'
            ]);

        });
    }
};
