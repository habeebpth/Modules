<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DwcGuestHotelReservationFactory;

class DwcGuestHotelReservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DwcGuestHotelReservationFactory
    {
        //return DwcGuestHotelReservationFactory::new();
    }

}
