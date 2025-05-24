<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\HmCheckinFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HmCheckin extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'hm_checkins';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'check_in',
        'check_out',
        'arrival_from',
        'booking_type_id',
        'booking_reference_id',
        'booking_reference_no',
        'purpose_of_visit',
        'remarks',
    ];

    protected static function newFactory(): HmCheckinFactory
    {
        //return HmCheckinFactory::new();
    }
    public function rooms()
    {
        return $this->hasMany(CheckinRoom::class, 'checkin_id');
    }

    public function guests()
    {
        return $this->hasManyThrough(
            HMGuests::class,
            CheckinRoom::class,
            'checkin_id', // Foreign key on CheckinRoom table
            'id', // Foreign key on HmGuest table
            'id', // Local key on HmCheckin table
            'guest_id' // Foreign key on CheckinGuest table
        );
    }
    public function checkinguest()
    {
        return $this->belongsToMany(HMGuests::class, 'checkin_guests', 'checkin_id', 'guest_id');
    }
}
