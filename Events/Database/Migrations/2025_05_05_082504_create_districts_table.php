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
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('region')->nullable()->comment('North, Central or South Kerala');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the districts table with Kerala districts (Ordered from South to North)
        $districts = [
            // Southern Kerala Districts
            [
                'name' => 'Thiruvananthapuram',
                'slug' => 'thiruvananthapuram',
                'region' => 'South Kerala',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Kollam',
                'slug' => 'kollam',
                'region' => 'South Kerala',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pathanamthitta',
                'slug' => 'pathanamthitta',
                'region' => 'South Kerala',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Alappuzha',
                'slug' => 'alappuzha',
                'region' => 'South Kerala',
                'display_order' => 4,
                'is_active' => true,
            ],

            // Central Kerala Districts
            [
                'name' => 'Kottayam',
                'slug' => 'kottayam',
                'region' => 'Central Kerala',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Idukki',
                'slug' => 'idukki',
                'region' => 'Central Kerala',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Ernakulam',
                'slug' => 'ernakulam',
                'region' => 'Central Kerala',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Thrissur',
                'slug' => 'thrissur',
                'region' => 'Central Kerala',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Palakkad',
                'slug' => 'palakkad',
                'region' => 'Central Kerala',
                'display_order' => 9,
                'is_active' => true,
            ],

            // Northern Kerala Districts
            [
                'name' => 'Malappuram',
                'slug' => 'malappuram',
                'region' => 'North Kerala',
                'display_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Kozhikode',
                'slug' => 'kozhikode',
                'region' => 'North Kerala',
                'display_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Wayanad',
                'slug' => 'wayanad',
                'region' => 'North Kerala',
                'display_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Kannur',
                'slug' => 'kannur',
                'region' => 'North Kerala',
                'display_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Kasaragod',
                'slug' => 'kasaragod',
                'region' => 'North Kerala',
                'display_order' => 14,
                'is_active' => true,
            ],
        ];

        DB::table('districts')->insert($districts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
