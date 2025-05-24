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
        Schema::table('dwc_guests', function (Blueprint $table) {
            $table->unsignedBigInteger('horse_id')->nullable()->change();
            $table->string('address_1')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwc_guests', function (Blueprint $table) {
            $table->dropColumn('horse_id');
            $table->dropColumn('address_1');
        });
    }
};
