<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DWCHotelRoomTypeFactory;

class DwcHotelRoomType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_room_types';
    protected $fillable = [];

    protected static function newFactory(): DWCHotelRoomTypeFactory
    {
        //return DWCHotelRoomTypeFactory::new();
    }
}
