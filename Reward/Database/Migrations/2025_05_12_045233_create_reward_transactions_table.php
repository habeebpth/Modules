<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reward_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('customer_id');
            $table->enum('transaction_type', ['Earn', 'Redeem', 'Adjust']);
            $table->integer('points');

            $table->string('reference_type', 50)->nullable(); // e.g. Purchase, Referral, Manual
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('transaction_date')->default(DB::raw('CURRENT_DATE'));
            $table->text('status')->nullable(); // e.g. active, hold, expired
            $table->text('earned_from')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_transactions');
    }
};
