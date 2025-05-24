<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DWCRacesTableSeeder extends Seeder
{
    public function run(): void
    {
        $races = [
            'AI World Cup',
            'Gines Dubai Sheema Classic',
            'AI Turf',
            'AI Golden Shaheen',
            'Quoz Sprint',
            'AI Gold Cup',
            'Dolphin Mile',
            'Derby',
            'AI Kahayla Classic'
        ];

        foreach ($races as $race) {
            DB::table('dwc_races')->insert([
                'name' => $race,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
