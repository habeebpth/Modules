<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;  // Import SoftDeletes
use Modules\HotelManagement\Database\factories\RoomTypeFactory;

class RoomType extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): RoomTypeFactory
    {
        //return RoomTypeFactory::new();
    }
}
