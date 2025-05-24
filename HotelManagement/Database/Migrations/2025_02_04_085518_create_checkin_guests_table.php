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
        Schema::create('checkin_guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkin_room_id');
            $table->unsignedBigInteger('checkin_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('guest_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_guests');
    }
};
