<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\RoomFacilityFactory;

class RoomFacility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'room_id',
        'facility_id',
    ];

    protected static function newFactory(): RoomFacilityFactory
    {
        //return RoomFacilityFactory::new();
    }
}
