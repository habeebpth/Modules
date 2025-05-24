<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DwcFlightTicketFactory;

class DwcFlightTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_flight_tickets';
    protected $fillable = [];

    protected $fillable = ['flight_no'];

    protected static function newFactory(): DwcFlightTicketFactory
    {
        //return DwcFlightTicketFactory::new();
    }
    public function departure()
    {
        return $this->belongsTo(DwcAirport::class, 'flight_from', 'id');
    }
    public function arrival()
    {
        return $this->belongsTo(DwcAirport::class, 'flight_to', 'id');
    }
    public function guests()
    {
        return $this->belongsToMany(DwcGuest::class, 'dwc_guest_flight_tickets', 'flight_ticket_id', 'guest_id');
    }
}
