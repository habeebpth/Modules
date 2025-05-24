<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\BookingTypeFactory;

class BookingType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): BookingTypeFactory
    {
        //return BookingTypeFactory::new();
    }
}
