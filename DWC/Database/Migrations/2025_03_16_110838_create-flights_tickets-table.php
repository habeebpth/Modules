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
        Schema::create('dwc_flight_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->string('flight_no', 50);
            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->date('arrival_date')->nullable();
            $table->time('arrival_time')->nullable();
            $table->string('flight_from', 100);
            $table->string('flight_to', 100);
            $table->string('flight_class', 50)->nullable(); // Economy, Business, First Class
            $table->string('locator', 50)->nullable(); // Booking reference
            $table->string('ticket_number', 50)->nullable();
            $table->text('note_1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwc_flight_tickets');
    }
};
