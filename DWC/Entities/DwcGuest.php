<?php

namespace Modules\DWC\Entities;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DwcGuestFactory;

class DwcGuest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_guests';
    protected $fillable = [];

    protected $appends = ['name_salutation'];

    protected static function newFactory(): DwcGuestFactory
    {
        //return DwcGuestFactory::new();
    }

    /**
     * Get the horse associated with the guest.
     */
    public function horse()
    {
        return $this->belongsTo(DwcHorse::class, 'horse_id');
    }
    public function guesttype()
    {
        return $this->belongsTo(DwcGuestType::class, 'guest_type');
    }
    public function guestcountry()
    {
        return $this->belongsTo(Country::class, 'country');
    }
    public function guestcountrycode()
    {
        return $this->belongsTo(Country::class, 'mobile_county_code');
    }
    public function guestnationality()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }
    public function flightTickets()
    {
        return $this->belongsToMany(DwcFlightTicket::class, 'dwc_guest_flight_tickets', 'guest_id', 'flight_ticket_id');
    }

    public function hotelReservations()
    {
        return $this->belongsToMany(DwcHotelReservation::class, 'dwc_guest_hotel_reservations', 'guest_id', 'hotel_reservation_id');
    }

    public function getNameSalutationAttribute()
    {
        return $this->salutation . ' ' . $this->first_name . ' ' . $this->last_name;
    }
}
