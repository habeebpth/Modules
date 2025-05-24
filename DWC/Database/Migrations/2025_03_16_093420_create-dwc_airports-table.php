<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDWCAirportsTable extends Migration
{
    public function up()
    {
        Schema::create('dwc_airports', function (Blueprint $table) {
            $table->id();
            $table->string('key', 10);
            $table->unsignedBigInteger('company_id')->default(1);
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->string('iata', 3);
            $table->string('icao', 4);
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->integer('altitude');
            $table->integer('timezone');
            $table->char('dst', 1); // For daylight saving time, typically 'Y' or 'N'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dwc_airports');
    }
}
