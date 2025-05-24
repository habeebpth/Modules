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
        Schema::create('dwc_hotel_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->string('hotel_id');
            $table->date('reservation_date')->nullable();
            $table->string('room_type')->nullable();
            $table->string('sharing_with')->nullable();
            $table->string('billing_code')->nullable();
            $table->integer('no_of_nights')->nullable();
            $table->date('billing_start_date')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->text('note_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwc_hotel_reservations');
    }
};
