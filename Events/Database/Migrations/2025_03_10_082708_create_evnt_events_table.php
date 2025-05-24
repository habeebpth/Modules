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
        Schema::create('evnt_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('label_color')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->enum('status', ['Upcoming', 'Completed', 'Cancelled'])->default('Upcoming');
            $table->string('banner')->nullable(); // Image
            $table->string('icon')->nullable(); // Image
            $table->string('brocher')->nullable(); // File
            $table->integer('maximum_participants')->nullable();
            $table->integer('maximum_participants_per_user')->nullable();
            $table->enum('registration_link_enable', ['Y', 'N'])->default('N');
            $table->dateTime('registration_last_date_time')->nullable();
            $table->enum('registration_fees_enable', ['Y', 'N'])->default('N');
            $table->decimal('registration_fees_amount', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evnt_events');
    }
};
