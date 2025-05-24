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
        Schema::create('hm_checkins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->string('arrival_from')->nullable();
            $table->unsignedBigInteger('booking_type_id');
            $table->unsignedBigInteger('booking_reference_id');
            $table->string('booking_reference_no')->nullable();
            $table->string('purpose_of_visit')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hm_checkins');
    }
};
