<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DWCHotelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert hotels data
        $hotels = [
            [
                'name' => 'The Meydan Hotel',
                'location' => 'Dubai, UAE',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
                'star_rating' => 5,
                'contact_number' => '+971 4 381 3111',
                'email' => 'info@meydan.ae',
                'total_rooms' => 284,
                'amenities' => 'Outdoor pool, Restaurants, Spa, Fitness center',
                'price_per_night' => 300.00
            ],
            [
                'name' => 'Hyatt Regency Creek',
                'location' => 'Dubai, UAE',
                'latitude' => 25.2603,
                'longitude' => 55.3320,
                'star_rating' => 4,
                'contact_number' => '+971 4 209 1234',
                'email' => 'info.dubai@hyatt.com',
                'total_rooms' => 421,
                'amenities' => 'Swimming pool, Restaurant, Lounge, Fitness center',
                'price_per_night' => 250.00
            ],
            [
                'name' => 'Sheraton Grand Sheikh Zayed',
                'location' => 'Dubai, UAE',
                'latitude' => 25.2354,
                'longitude' => 55.3060,
                'star_rating' => 5,
                'contact_number' => '+971 4 607 1111',
                'email' => 'info.sheraton.com',
                'total_rooms' => 654,
                'amenities' => 'Pool, Lounge, Bar, Business center, Free Wi-Fi',
                'price_per_night' => 350.00
            ],
            [
                'name' => 'Mövenpick Burdubai',
                'location' => 'Dubai, UAE',
                'latitude' => 25.2430,
                'longitude' => 55.3205,
                'star_rating' => 4,
                'contact_number' => '+971 4 336 6000',
                'email' => 'info@movenpick.com',
                'total_rooms' => 255,
                'amenities' => 'Spa, Pool, Dining, Fitness center',
                'price_per_night' => 220.00
            ]
        ];

        // Insert into hotels table
        foreach ($hotels as $hotel) {
            $hotel_id = DB::table('dwc_hotels')->insertGetId($hotel);

            // Insert room types data for each hotel
            $room_types = [];

            if ($hotel['name'] == 'The Meydan Hotel') {
                $room_types = [
                    ['hotel_id' => $hotel_id, 'room_type' => 'Meydan Room', 'max_occupancy' => 2, 'price_per_night' => 350.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, City view'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Meydan Balcony Room', 'max_occupancy' => 2, 'price_per_night' => 380.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Balcony, City view'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Meydan Suite', 'max_occupancy' => 4, 'price_per_night' => 500.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Living area, Premium services'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Executive Suite', 'max_occupancy' => 4, 'price_per_night' => 600.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Larger living area, Premium services']
                ];
            } elseif ($hotel['name'] == 'Hyatt Regency Creek') {
                $room_types = [
                    ['hotel_id' => $hotel_id, 'room_type' => 'Standard Room', 'max_occupancy' => 2, 'price_per_night' => 270.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Creek View Room', 'max_occupancy' => 2, 'price_per_night' => 300.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Creek view'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Club Room', 'max_occupancy' => 2, 'price_per_night' => 350.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Regency Club Lounge access'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Regency Suite', 'max_occupancy' => 4, 'price_per_night' => 450.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Living area, Premium facilities']
                ];
            } elseif ($hotel['name'] == 'Sheraton Grand Sheikh Zayed') {
                $room_types = [
                    ['hotel_id' => $hotel_id, 'room_type' => 'Classic Room', 'max_occupancy' => 2, 'price_per_night' => 370.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Club Room', 'max_occupancy' => 2, 'price_per_night' => 420.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Sheraton Club Lounge access'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'One-Bedroom Suite', 'max_occupancy' => 3, 'price_per_night' => 550.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Separate living area'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Presidential Suite', 'max_occupancy' => 4, 'price_per_night' => 750.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Luxury services, Large living area']
                ];
            } elseif ($hotel['name'] == 'Mövenpick Burdubai') {
                $room_types = [
                    ['hotel_id' => $hotel_id, 'room_type' => 'Superior Room', 'max_occupancy' => 2, 'price_per_night' => 240.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Executive Room', 'max_occupancy' => 2, 'price_per_night' => 280.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Access to executive services'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Junior Suite', 'max_occupancy' => 3, 'price_per_night' => 350.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Separate living area'],
                    ['hotel_id' => $hotel_id, 'room_type' => 'Family Suite', 'max_occupancy' => 4, 'price_per_night' => 450.00, 'amenities' => 'Free Wi-Fi, TV, Air conditioning, Family-friendly amenities']
                ];
            }

            // Insert room types for each hotel
            foreach ($room_types as $room) {
                DB::table('dwc_room_types')->insert($room);
            }
        }
    }
}
