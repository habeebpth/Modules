<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DWCAirportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = storage_path('app/airports.csv');
        $csvData = file_get_contents($csvFile);
        $rows = explode("\n", $csvData);
        foreach ($rows as $row) {
            $columns = str_getcsv($row);
            if (count($columns) == 11) {
                DB::table('dwc_airports')->insert([
                    'key' => $columns[0],
                    'name' => $columns[1],
                    'city' => $columns[2],
                    'country' => $columns[3],
                    'iata' => $columns[4],
                    'icao' => $columns[5],
                    'latitude' => $columns[6],
                    'longitude' => $columns[7],
                    'altitude' => $columns[8],
                    'timezone' => $columns[9],
                    'dst' => $columns[10]
                ]);
            }
        }
    }
}
