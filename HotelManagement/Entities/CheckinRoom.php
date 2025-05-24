<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\CheckinRoomFactory;

class CheckinRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'checkin_id',
        'room_id',
        'room_type_id', // Add this field
        'adults',
        'children',
        'extra_bed',
        'adults',
        'children',
        'checkin',
        'checkout',
        'rent',
    ];

    protected static function newFactory(): CheckinRoomFactory
    {
        //return CheckinRoomFactory::new();
    }
    public function checkin()
    {
        return $this->belongsTo(HmCheckin::class, 'checkin_id');
    }

    public function guests()
    {
        return $this->hasMany(CheckinGuest::class, 'checkin_room_id');
    }
}
