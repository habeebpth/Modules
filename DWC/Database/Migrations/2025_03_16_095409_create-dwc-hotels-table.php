<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        // Create hotels table
        Schema::create('dwc_hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->string('name')->unique();
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('star_rating')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->integer('total_rooms')->nullable();
            $table->text('amenities')->nullable();
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->timestamps();
        });

        // Create room_types table
        Schema::create('dwc_room_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->unsignedBigInteger('hotel_id');
            $table->string('room_type')->nullable();
            $table->integer('max_occupancy')->nullable();
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->text('amenities')->nullable();
            $table->timestamps();


        });
    }

    public function down()
    {
        // Drop room_types table first as it has a foreign key constraint
        Schema::dropIfExists('dwc_room_types');
        Schema::dropIfExists('dwc_hotels');
    }
};
