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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->string('vehicle_number')->unique();
            $table->string('vehicle_code')->unique();
            $table->integer('no_of_seats');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('company_id');
            $table->char('disable', 1)->default('y');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
