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
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('mobile');
            $table->integer('country_wtsap_phonecode')->nullable()->after('country_phonecode');
            $table->string('place')->nullable()->after('whatsapp');
            $table->string('pincode')->nullable()->after('place');
            $table->integer('age')->nullable()->after('pincode');
            $table->enum('sex', ['male', 'female', 'other'])->nullable()->after('age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
            $table->dropColumn('country_wtsap_phonecode');
            $table->dropColumn('place');
            $table->dropColumn('pincode');
            $table->dropColumn('age');
            $table->dropColumn('sex');
        });
    }
};
