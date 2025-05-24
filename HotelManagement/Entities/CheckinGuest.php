<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\CheckinGuestFactory;

class CheckinGuest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'checkin_room_id', // Add this field
        'guest_id',
        'checkin_id', // Add this field
        'room_id',
    ];
    protected static function newFactory(): CheckinGuestFactory
    {
        //return CheckinGuestFactory::new();
    }
    public function room()
    {
        return $this->belongsTo(CheckinRoom::class, 'checkin_room_id');
    }

    public function guest()
    {
        return $this->belongsTo(HMGuests::class, 'guest_id');
    }
}
