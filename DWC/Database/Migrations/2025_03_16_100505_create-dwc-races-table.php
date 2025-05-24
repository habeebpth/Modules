<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('dwc_races', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->string('name')->unique(); // Name of the cup
            $table->text('description')->nullable(); // Cup details
            $table->date('event_date')->nullable(); // Date of the competition
            $table->decimal('prize_amount', 10, 2)->nullable(); // Prize money
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dwc_races');
    }
};
