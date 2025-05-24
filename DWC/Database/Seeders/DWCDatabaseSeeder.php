<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;

class DWCDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DWCAirportsSeeder::class,
            DWCHotelsSeeder::class,
            // DWCHorsesTableSeeder::class,
            // DWCRacesTableSeeder::class,
            DWCHorseRaceSeeder::class,
            DwcGuestTypeSeeder::class,
            DwcBillingCodeSeeder::class
        ]);
    }
}
