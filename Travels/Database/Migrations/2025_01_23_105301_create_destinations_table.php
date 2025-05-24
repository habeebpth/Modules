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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedBigInteger('country_id');
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('hashname', 255)->nullable(); // Add hashname column
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
        Schema::dropIfExists('destinations');
    }
};
