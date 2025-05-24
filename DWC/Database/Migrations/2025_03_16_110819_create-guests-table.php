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
        Schema::create('dwc_guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->unsignedBigInteger('horse_id');
            $table->date('amendment_date')->nullable();
            $table->string('guest_type', 50)->nullable();  // New guest type column
            $table->string('last_name', 100)->nullable();
            $table->string('first_name', 100);
            $table->string('title', 50)->nullable();
            $table->string('salutation', 50)->nullable();

            $table->string('company', 255)->nullable();
            $table->string('address_1', 255);
            $table->string('address_2', 255)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country', 100);
            $table->string('tel', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('mobile_county_code', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->boolean('visa_required')->default(false);
            $table->string('passport_number', 50)->nullable();
            $table->unsignedBigInteger('travel_with')->nullable();
            $table->timestamps(0); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwc_guests');
    }
};
