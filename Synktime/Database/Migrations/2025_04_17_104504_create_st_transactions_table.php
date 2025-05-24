<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('st_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->dateTime('punch_time')->nullable();
            $table->string('punch_state');
            $table->string('attendance_type')->nullable();
            $table->string('punch_state_display')->nullable();
            $table->string('work_code')->nullable();
            $table->string('gps_location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('location_name')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('device_type')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('device_sn')->nullable();
            $table->string('device_name')->nullable();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('added_by_id')->nullable();
            $table->string('added_by_name')->nullable();
            $table->text('remarks')->nullable();
            $table->text('comments')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('st_transactions');
    }
};
