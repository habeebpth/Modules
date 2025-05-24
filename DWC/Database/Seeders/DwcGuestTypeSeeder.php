<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DwcGuestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestTypes = [
            'Owner',
            'Trainer',
            'Jockey',
            'Stable staff',
            'TV Broadcast',
            'International Media',
            'Media Notes',
            'Official Guest',
            'Official Guest-ARF',
            'Sponsors',
            'Working Personnel',
            'SPG-Owner',
            'SPG-Trainer',
            'SPG-Jockey',
            'SPG-Stable Staff'
        ];

        foreach ($guestTypes as $index => $type) {
            DB::table('dwc_guest_types')->insert([
                'company_id' => 1, // Adjust this if needed
                'name' => $type,
                'position' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
