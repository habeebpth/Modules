<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('dwc_race_horse', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dwc_races_id');
            $table->unsignedBigInteger('dwc_horse_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dwc_race_horse');
    }
};
