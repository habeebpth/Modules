<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('employee_payroll_cycles')) {
            Schema::create('employee_payroll_cycles', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedBigInteger('payroll_cycle_id')->nullable();
                $table->foreign('payroll_cycle_id')->references('id')->on('payroll_cycles')->onDelete('cascade')->onUpdate('cascade');

                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_payroll_cycle');
    }
};
