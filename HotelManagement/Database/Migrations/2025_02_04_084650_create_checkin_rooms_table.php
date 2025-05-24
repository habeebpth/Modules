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
        Schema::create('checkin_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkin_id');
            $table->date('checkin');
            $table->date('checkout');
            $table->decimal('rent', 10, 2);
            $table->integer('extra_bed')->default(0);
            $table->unsignedBigInteger('room_type_id');
            $table->unsignedBigInteger('room_id');
            $table->integer('adults');
            $table->integer('children')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_rooms');
    }
};
